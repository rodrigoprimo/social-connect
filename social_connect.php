<?php
/*
Plugin Name: Social Connect
Plugin URI: http://github.com/ashwinphatak/wp_social_connect
Description: Allows users to register and login using their existing Twitter, Facebook, Google and wordpress.com accounts
Version: 1.0
Author: Ashwin Phatak
Author URI: http://github.com/ashwinphatak
License: GPL2
*/
?>
<?php

function add_stylesheets()
{
  wp_register_style("social_connect", plugins_url() . "/wp_social_connect/media/css/style.css");
  wp_enqueue_style("social_connect");
}

function add_javascripts()
{
  wp_enqueue_script("jquery"); // WordPress already has jquery files

  wp_register_script("social_connect", plugins_url() . "/wp_social_connect/media/js/connect.js");
  wp_enqueue_script("social_connect");
}

add_action('wp_print_styles', 'add_stylesheets');
add_action('wp_print_scripts', 'add_javascripts');

?>