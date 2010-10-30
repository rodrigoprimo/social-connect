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
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-dialog');
  

  wp_register_script("social_connect", plugins_url() . "/wp_social_connect/media/js/connect.js");
  wp_enqueue_script("social_connect");
}

function social_connect_admin_menu() {
  add_options_page('Social Connect Settings', 'Social Connect', 'manage_options', 'social-connect-id', 'render_social_connect_settings');
	add_action( 'admin_init', 'register_social_connect_settings' );

}

function register_social_connect_settings() 
{
	register_setting( 'social-connect-settings-group', 'facebook_api_key' );
	register_setting( 'social-connect-settings-group', 'facebook_secret_key' );
}

function render_social_connect_settings() 
{
?>
<div class="wrap">
<h2>Social Connect Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'social-connect-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Facebook API Key</th>
        <td><input type="text" name="facebook_api_key" value="<?php echo get_option('facebook_api_key'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Facebook Secret Key</th>
        <td><input type="text" name="facebook_secret_key" value="<?php echo get_option('facebook_secret_key'); ?>" /></td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div> <?php 
}


add_action('wp_print_styles', 'add_stylesheets');
add_action('wp_print_scripts', 'add_javascripts');
add_action('admin_menu', 'social_connect_admin_menu');

?>