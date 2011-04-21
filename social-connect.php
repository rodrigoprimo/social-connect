<?php
/*
Plugin Name: Social Connect
Plugin URI: http://wordpress.org/extend/plugins/social-connect/
Description: Allow your visitors to comment, login and register with their Twitter, Facebook, Google, Yahoo or WordPress.com account.
Version: 0.6
Author: Brent Shepherd
Author URI: http://wordpress.org/extend/plugins/social-connect/
License: GPL2
*/


/** 
 * Check technical requirements are fulfilled before activating.
 **/
function sc_activate(){
	if ( !function_exists( 'register_post_status' ) || !function_exists( 'curl_version' ) || version_compare( PHP_VERSION, '5.1.2', '<' ) ) {
		deactivate_plugins( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
		if( !function_exists( 'register_post_status' ) )
			wp_die(__( "Sorry, but you can not run Social Connect. It requires WordPress 3.0 or newer. Consider <a href='http://codex.wordpress.org/Updating_WordPress'>upgrading</a> your WordPress installation, it's worth the effort.<br/><a href=" . admin_url( 'plugins.php' ) . ">Return to Plugins Admin page &raquo;</a>"), 'prospress' );
		elseif( !function_exists( 'curl_version' ) )
			wp_die(__( "Sorry, but you can not run Social Connect. It requires the <a href='http://www.php.net/manual/en/intro.curl.php'>PHP libcurl extension</a> be installed. Please contact your web host and request libcurl be <a href='http://www.php.net/manual/en/intro.curl.php'>installed</a>.<br/><a href=" . admin_url( 'plugins.php' ) . ">Return to Plugins Admin page &raquo;</a>"), 'prospress' );
		else
			wp_die(__( "Sorry, but you can not run Social Connect. It requires PHP 5.1.2 or newer. Please contact your web host and request they <a href='http://www.php.net/manual/en/migration5.php'>migrate</a> your PHP installation to run Social Connect.<br/><a href=" . admin_url( 'plugins.php' ) . ">Return to Plugins Admin page &raquo;</a>"), 'prospress' );
	}
	do_action( 'sc_activation' );
}
register_activation_hook( __FILE__, 'sc_activate' );


/*
 * Registration.php is deprecated since version 3.1 with no alternative available.
 * registration.php functions moved to user.php, everything is now included by default
 * This file only need to be included for versions before 3.1.
 */
global $wp_version;
if( !function_exists( 'email_exists' ) )
	require_once( ABSPATH . WPINC . '/registration.php' );

require_once(dirname(__FILE__) . '/constants.php' );
require_once(dirname(__FILE__) . '/utils.php' );
require_once(dirname(__FILE__) . '/media.php' );
require_once(dirname(__FILE__) . '/admin.php' );
require_once(dirname(__FILE__) . '/ui.php' );


function sc_social_connect_process_login( $is_ajax = false )
{
	if ( isset( $_REQUEST['redirect_to'] ) && $_REQUEST['redirect_to'] != '' ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// Redirect to https if user wants ssl
		if ( isset($secure_cookie) && $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
		$redirect_to = admin_url();
	}
  
  $social_connect_provider = $_REQUEST['social_connect_provider'];
  $sc_provider_identity_key = 'social_connect_' . $social_connect_provider . '_id';
  $sc_provided_signature =  $_REQUEST['social_connect_signature'];
  
  switch($social_connect_provider) {
    case 'facebook':
      social_connect_verify_signature($_REQUEST['social_connect_access_token'], $sc_provided_signature, $redirect_to);
      $fb_json = json_decode(file_get_contents("https://graph.facebook.com/me?access_token=" . $_REQUEST['social_connect_access_token']));
      $sc_provider_identity = $fb_json->{'id'};
      $sc_email = $fb_json->{'email'};
      $sc_first_name = $fb_json->{'first_name'};
      $sc_last_name = $fb_json->{'last_name'};
      $sc_profile_url = $fb_json->{'link'};
      $sc_name = $sc_first_name . ' ' . $sc_last_name;
      $user_login = strtolower($sc_first_name.$sc_last_name);
    break;

    case 'twitter':
      $sc_provider_identity = $_REQUEST['social_connect_twitter_identity'];
      social_connect_verify_signature($sc_provider_identity, $sc_provided_signature, $redirect_to);
      $sc_name = $_REQUEST['social_connect_name'];
      $names = explode(" ", $sc_name);
      $sc_first_name = $names[0];
      $sc_last_name = $names[1];

      $sc_screen_name = $_REQUEST['social_connect_screen_name'];
      $sc_profile_url = '';

      // get host name from URL http://in.php.net/preg_match
      preg_match("/^(http:\/\/)?([^\/]+)/i", site_url(), $matches);
      $host = $matches[2];
      preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
      $domain_name = $matches[0];

      $sc_email = 'tw_' . md5($sc_provider_identity) . '@' . $domain_name;
      $user_login = $sc_screen_name;
    break;
    
    case 'google':
      $sc_provider_identity = $_REQUEST['social_connect_openid_identity'];
      social_connect_verify_signature($sc_provider_identity, $sc_provided_signature, $redirect_to);
      $sc_email = $_REQUEST['social_connect_email'];
      $sc_first_name = $_REQUEST['social_connect_first_name'];
      $sc_last_name = $_REQUEST['social_connect_last_name'];
      $sc_profile_url = '';
      $sc_name = $sc_first_name . ' ' . $sc_last_name;
      $user_login = strtolower($sc_first_name.$sc_last_name);
    break;

    case 'yahoo':
      $sc_provider_identity = $_REQUEST['social_connect_openid_identity'];
      social_connect_verify_signature($sc_provider_identity, $sc_provided_signature, $redirect_to);
      $sc_email = $_REQUEST['social_connect_email'];
      $sc_name = $_REQUEST['social_connect_name'];
      $sc_username = $_REQUEST['social_connect_username'];
      $sc_profile_url = '';
      if($sc_name == '') {
        if($sc_username == '') {
          $names = explode("@", $sc_email);
          $sc_name = $names[0];
          $sc_first_name = $sc_name;
          $sc_last_name = '';
        } else {
          $names = explode(" ", $sc_username);
          $sc_first_name = $names[0];
          $sc_last_name = $names[1];
        }
      } else {
        $names = explode(" ", $sc_name);
        $sc_first_name = $names[0];
        $sc_last_name = $names[1];
      }
      $user_login = strtolower($sc_first_name.$sc_last_name);
    break;

    case 'wordpress':
      $sc_provider_identity = $_REQUEST['social_connect_openid_identity'];
      social_connect_verify_signature($sc_provider_identity, $sc_provided_signature, $redirect_to);
      $sc_email = $_REQUEST['social_connect_email'];
      $sc_name = $_REQUEST['social_connect_name'];
      $sc_profile_url = '';
      if(trim($sc_name) == '') {
        $names = explode("@", $sc_email);
        $sc_name = $names[0];
        $sc_first_name = $sc_name;
        $sc_last_name = '';
      } else {
        $names = explode(" ", $sc_name);
        $sc_first_name = $names[0];
        $sc_last_name = $names[1];
      }
      
      $user_login = strtolower($sc_first_name.$sc_last_name);

      setcookie("social_connect_wordpress_blog_url", $sc_provider_identity, time()+3600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);

    break;
  }


  // cookies used to display welcome message if already signed in recently using some provider
  setcookie("social_connect_current_provider", $social_connect_provider, time()+3600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);
  if ($sc_name == '') {
    setcookie("social_connect_current_name", $user_login, time()+3600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);
  } else {
    setcookie("social_connect_current_name", $sc_name, time()+3600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);
  }
  
  // get user by meta
  $user_id = social_connect_get_user_by_meta($sc_provider_identity_key, $sc_provider_identity);
  if($user_id) {
    // user already exists, just log him in
    wp_set_auth_cookie($user_id);
    do_action('social_connect_login', $user_login );
	if( $is_ajax )
		echo '{"redirect":"' . $redirect_to . '"}';
	else
		wp_safe_redirect($redirect_to);
    exit();
  }
  
  // user not found by provider identity, check by email
  if( ( $user_id = email_exists( $sc_email ) ) ) {
    // user already exists, associate with provider identity
    update_user_meta($user_id, $sc_provider_identity_key, $sc_provider_identity);
    
    // user signed in with provider identity after normal WP signup. Since email is verified, sign him in
    wp_set_auth_cookie($user_id);
    do_action('social_connect_login', $user_login );
	if( $is_ajax )
		echo '{"redirect":"' . $redirect_to . '"}';
	else
		wp_safe_redirect($redirect_to);
    exit();
    
  } else {
    // create new user and associate provider identity
    if(username_exists($user_login)) {
      $user_login = strtolower("sc_". md5($social_connect_provider . $sc_provider_identity));
    }

    $userdata = array('user_login' => $user_login, 'user_email' => $sc_email, 'first_name' => $sc_first_name, 'last_name' => $sc_last_name,
      'user_url' => $sc_profile_url, 'user_pass' => wp_generate_password());
    
    // create a new user
    $user_id = wp_insert_user($userdata);
    
    if($user_id && is_integer($user_id)) {
      update_user_meta($user_id, $sc_provider_identity_key, $sc_provider_identity);
    
      wp_set_auth_cookie($user_id);
      do_action('social_connect_login', $user_login );
	if( $is_ajax )
		echo '{"redirect":"' . $redirect_to . '"}';
	else
		wp_safe_redirect($redirect_to);
      exit();
    }
  }
}
// Hook to 'login_form_' . $action
add_action('login_form_social_connect', 'sc_social_connect_process_login');

// Handle calls from plugins that use an Ajax for login
function sc_ajax_login(){
	if( isset( $_POST[ 'login_submit' ] ) && $_POST[ 'login_submit' ] == 'ajax' && // Plugins will need to pass this param
		isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'social_connect' )
		sc_social_connect_process_login( true );
}
add_action('init', 'sc_ajax_login');
