<?php
require_once( dirname( dirname( dirname( dirname( __FILE__ )))) . '/wp-load.php' );

if( !defined( 'SOCIAL_CONNECT_PLUGIN_URL' )) {
  define( 'SOCIAL_CONNECT_PLUGIN_URL', plugins_url() . '/' . basename( dirname( __FILE__ )));
}

?>