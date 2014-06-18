<?php

if( !defined( 'SOCIAL_CONNECT_PLUGIN_URL' )) {
  define( 'SOCIAL_CONNECT_PLUGIN_URL', plugins_url() . '/' . basename( dirname( __FILE__ )));
}

define( 'SOCIAL_CONNECT_GOOGLE_PLUS_REDIRECT_URL', SOCIAL_CONNECT_PLUGIN_URL . '/google-plus/callback.php' );