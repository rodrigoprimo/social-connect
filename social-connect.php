<?php
/*
Plugin Name: Social Connect
Plugin URI: http://wordpress.org/extend/plugins/social-connect/
Description: Allow your visitors to comment, login and register with their Twitter, Facebook, Google, Yahoo or WordPress.com account.
Version: 0.9
Author: Brent Shepherd
Author URI: http://wordpress.org/extend/plugins/social-connect/
License: GPL2
 */


/** 
 * Check technical requirements are fulfilled before activating.
 **/
function sc_activate(){
	if ( !function_exists( 'register_post_status' ) || !function_exists( 'curl_version' ) || !function_exists( 'hash' ) || version_compare( PHP_VERSION, '5.1.2', '<' ) ) {
		deactivate_plugins( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
		if ( !function_exists( 'register_post_status' ) )
			wp_die( sprintf( __( "Sorry, but you can not run Social Connect. It requires WordPress 3.0 or newer. Consider <a href='http://codex.wordpress.org/Updating_WordPress'>upgrading</a> your WordPress installation, it's worth the effort.<br/><a href=\"%s\">Return to Plugins Admin page &raquo;</a>", 'social_connect'), admin_url( 'plugins.php' ) ), 'social-connect' );
		elseif ( !function_exists( 'curl_version' ) )
			wp_die( sprintf( __( "Sorry, but you can not run Social Connect. It requires the <a href='http://www.php.net/manual/en/intro.curl.php'>PHP libcurl extension</a> be installed. Please contact your web host and request libcurl be <a href='http://www.php.net/manual/en/intro.curl.php'>installed</a>.<br/><a href=\"%s\">Return to Plugins Admin page &raquo;</a>", 'social_connect'), admin_url( 'plugins.php' ) ), 'social-connect' );
		elseif ( !function_exists( 'hash' ) )
			wp_die( sprintf( __( "Sorry, but you can not run Social Connect. It requires the <a href='http://www.php.net/manual/en/intro.hash.php'>PHP Hash Engine</a>. Please contact your web host and request Hash engine be <a href='http://www.php.net/manual/en/hash.setup.php'>installed</a>.<br/><a href=\"%s\">Return to Plugins Admin page &raquo;</a>", 'social_connect'), admin_url( 'plugins.php' ) ), 'social-connect' );
		else
			wp_die( sprintf( __( "Sorry, but you can not run Social Connect. It requires PHP 5.1.2 or newer. Please contact your web host and request they <a href='http://www.php.net/manual/en/migration5.php'>migrate</a> your PHP installation to run Social Connect.<br/><a href=\"%s\">Return to Plugins Admin page &raquo;</a>", 'social_connect'), admin_url( 'plugins.php' ) ), 'social-connect' );
	}
	do_action( 'sc_activation' );
}
register_activation_hook( __FILE__, 'sc_activate' );


/**
 * Registration.php is deprecated since version 3.1 with no alternative available.
 * registration.php functions moved to user.php, everything is now included by default
 * This file only need to be included for versions before 3.1.
 */
if ( !function_exists( 'email_exists' ) )
	require_once( ABSPATH . WPINC . '/registration.php' );

/**
 * Internationalization of the plugin
 **/
function sc_social_connect_l10n() {
	$plugin_dir = basename( dirname( __FILE__ ) );
	load_plugin_textdomain( 'social_connect', null, "$plugin_dir/languages" );
}
add_action( 'init', 'sc_social_connect_l10n', -1000 );

require_once( dirname( __FILE__ ) . '/constants.php' );
require_once( dirname( __FILE__ ) . '/utils.php' );
require_once( dirname( __FILE__ ) . '/media.php' );
require_once( dirname( __FILE__ ) . '/admin.php' );
require_once( dirname( __FILE__ ) . '/ui.php' );


function sc_social_connect_process_login( $is_ajax = false ){
	if ( isset( $_REQUEST[ 'redirect_to' ] ) && $_REQUEST[ 'redirect_to' ] != '' ) {
		$redirect_to = $_REQUEST[ 'redirect_to' ];
		// Redirect to https if user wants ssl
		if ( isset( $secure_cookie ) && $secure_cookie && false !== strpos( $redirect_to, 'wp-admin') )
			$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
	} else {
		$redirect_to = admin_url();
	}
	$redirect_to = apply_filters( 'social_connect_redirect_to', $redirect_to );

	$social_connect_provider = $_REQUEST[ 'social_connect_provider' ];
	$sc_provider_identity_key = 'social_connect_' . $social_connect_provider . '_id';
	$sc_provided_signature =  $_REQUEST[ 'social_connect_signature' ];

	switch( $social_connect_provider ) {
		case 'facebook':
		social_connect_verify_signature( $_REQUEST[ 'social_connect_access_token' ], $sc_provided_signature, $redirect_to );
		$fb_json = json_decode( sc_curl_get_contents("https://graph.facebook.com/me?access_token=" . $_REQUEST[ 'social_connect_access_token' ]) );
		$sc_provider_identity = $fb_json->{ 'id' };
		$sc_email = $fb_json->{ 'email' };
		$sc_first_name = $fb_json->{ 'first_name' };
		$sc_last_name = $fb_json->{ 'last_name' };
		$sc_profile_url = $fb_json->{ 'link' };
		$sc_name = $sc_first_name . ' ' . $sc_last_name;
		$user_login = strtolower( $sc_first_name.$sc_last_name );
		break;

		case 'twitter':
		$sc_provider_identity = $_REQUEST[ 'social_connect_twitter_identity' ];
		social_connect_verify_signature( $sc_provider_identity, $sc_provided_signature, $redirect_to );
		$sc_name = $_REQUEST[ 'social_connect_name' ];
		$names = explode(" ", $sc_name );
		$sc_first_name = $names[0];
		$sc_last_name = $names[1];
		$sc_screen_name = $_REQUEST[ 'social_connect_screen_name' ];
		$sc_profile_url = '';
		// Get host name from URL
		$site_url = parse_url( site_url() );
		$sc_email = 'tw_' . md5( $sc_provider_identity ) . '@' . $site_url['host'];
		$user_login = $sc_screen_name;
		break;

		case 'google':
		$sc_provider_identity = $_REQUEST[ 'social_connect_openid_identity' ];
		social_connect_verify_signature( $sc_provider_identity, $sc_provided_signature, $redirect_to );
		$sc_email = $_REQUEST[ 'social_connect_email' ];
		$sc_first_name = $_REQUEST[ 'social_connect_first_name' ];
		$sc_last_name = $_REQUEST[ 'social_connect_last_name' ];
		$sc_profile_url = '';
		$sc_name = $sc_first_name . ' ' . $sc_last_name;
		$user_login = strtolower( $sc_first_name.$sc_last_name );
		break;

		case 'yahoo':
		$sc_provider_identity = $_REQUEST[ 'social_connect_openid_identity' ];
		social_connect_verify_signature( $sc_provider_identity, $sc_provided_signature, $redirect_to );
		$sc_email = $_REQUEST[ 'social_connect_email' ];
		$sc_name = $_REQUEST[ 'social_connect_name' ];
		$sc_username = $_REQUEST[ 'social_connect_username' ];
		$sc_profile_url = '';
		if ( $sc_name == '') {
			if ( $sc_username == '') {
				$names = explode("@", $sc_email );
				$sc_name = $names[0];
				$sc_first_name = $sc_name;
				$sc_last_name = '';
			} else {
				$names = explode(" ", $sc_username );
				$sc_first_name = $names[0];
				$sc_last_name = $names[1];
			}
		} else {
			$names = explode(" ", $sc_name );
			$sc_first_name = $names[0];
			$sc_last_name = $names[1];
		}
		$user_login = strtolower( $sc_first_name.$sc_last_name );
		break;

		case 'wordpress':
		$sc_provider_identity = $_REQUEST[ 'social_connect_openid_identity' ];
		social_connect_verify_signature( $sc_provider_identity, $sc_provided_signature, $redirect_to );
		$sc_email = $_REQUEST[ 'social_connect_email' ];
		$sc_name = $_REQUEST[ 'social_connect_name' ];
		$sc_profile_url = '';
		if ( trim( $sc_name ) == '') {
			$names = explode("@", $sc_email );
			$sc_name = $names[0];
			$sc_first_name = $sc_name;
			$sc_last_name = '';
		} else {
			$names = explode(" ", $sc_name );
			$sc_first_name = $names[0];
			$sc_last_name = $names[1];
		}
		$user_login = strtolower( $sc_first_name.$sc_last_name );
		break;
	}

	// Cookies used to display welcome message if already signed in recently using some provider
	setcookie("social_connect_current_provider", $social_connect_provider, time()+3600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true );

	// Get user by meta
	$user_id = social_connect_get_user_by_meta( $sc_provider_identity_key, $sc_provider_identity );
	if ( $user_id ) {
		$user_data  = get_userdata( $user_id );
		$user_login = $user_data->user_login;
	} elseif ( $user_id = email_exists( $sc_email ) ) { // User not found by provider identity, check by email
		update_user_meta( $user_id, $sc_provider_identity_key, $sc_provider_identity );

		$user_data  = get_userdata( $user_id );
		$user_login = $user_data->user_login;

	} else { // Create new user and associate provider identity
		if ( username_exists( $user_login ) )
			$user_login = apply_filters( 'social_connect_username_exists', strtolower("sc_". md5( $social_connect_provider . $sc_provider_identity ) ) );

		$userdata = array( 'user_login' => $user_login, 'user_email' => $sc_email, 'first_name' => $sc_first_name, 'last_name' => $sc_last_name, 'user_url' => $sc_profile_url, 'user_pass' => wp_generate_password() );

		// Create a new user
		$user_id = wp_insert_user( $userdata );

		if ( $user_id && is_integer( $user_id ) )
			update_user_meta( $user_id, $sc_provider_identity_key, $sc_provider_identity );
	}

	wp_set_auth_cookie( $user_id );

	do_action( 'social_connect_login', $user_login );

	if ( $is_ajax )
		echo '{"redirect":"' . $redirect_to . '"}';
	else
		wp_safe_redirect( $redirect_to );
	exit();
}
// Hook to 'login_form_' . $action
add_action( 'login_form_social_connect', 'sc_social_connect_process_login');

// Handle calls from plugins that use an Ajax for login
function sc_ajax_login(){
	if ( isset( $_POST[ 'login_submit' ] ) && $_POST[ 'login_submit' ] == 'ajax' && // Plugins will need to pass this param
	isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'social_connect' )
		sc_social_connect_process_login( true );
}
add_action( 'init', 'sc_ajax_login');
