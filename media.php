<?php

function sc_add_stylesheets(){
	if( !wp_style_is( 'social_connect', 'registered' ) ) {
		wp_register_style( "social_connect", SOCIAL_CONNECT_PLUGIN_URL . "/media/css/style.css" );
	}

	if ( did_action( 'wp_print_styles' ) ) {
		wp_print_styles( 'social_connect' );
		wp_print_styles( 'wp-jquery-ui-dialog' );
	} else {
		wp_enqueue_style( "social_connect" );
		wp_enqueue_style( "wp-jquery-ui-dialog" );
	}
}
add_action( 'login_enqueue_scripts', 'sc_add_stylesheets' );
add_action( 'wp_head', 'sc_add_stylesheets' );


function sc_add_admin_stylesheets(){
	if( !wp_style_is( 'social_connect', 'registered' ) ) {
		wp_register_style( "social_connect", SOCIAL_CONNECT_PLUGIN_URL . "/media/css/style.css" );
	}

	if ( did_action( 'wp_print_styles' )) {
		wp_print_styles( 'social_connect' );
	} else {
		wp_enqueue_style( "social_connect" );
	}
}
add_action( 'admin_print_styles', 'sc_add_admin_stylesheets' );


function sc_add_javascripts(){
	$deps = array( 'jquery', 'jquery-ui-core' );
	$wordpress_enabled = get_option( 'social_connect_wordpress_enabled' );
	
	if ( $wordpress_enabled ) {
		$deps[] = 'jquery-ui-dialog';
	}

	if( ! wp_script_is( 'social_connect', 'registered' ) )
		wp_register_script( 'social_connect', SOCIAL_CONNECT_PLUGIN_URL . '/media/js/connect.js', $deps );

	wp_enqueue_script( 'social_connect' );
	wp_localize_script( 'social_connect', 'social_connect_data', array( 'wordpress_enabled' => $wordpress_enabled ) );
}
add_action( 'login_enqueue_scripts', 'sc_add_javascripts' );
add_action( 'wp_enqueue_scripts', 'sc_add_javascripts' );
