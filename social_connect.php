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

function sc_add_stylesheets()
{
  if(!wp_style_is('social_connect', 'registered') ) {
    wp_register_style("social_connect", plugins_url() . "/wp_social_connect/media/css/style.css");
    wp_register_style("jquery-ui", 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/themes/smoothness/jquery-ui.css');
  }

  if (did_action('wp_print_styles')) {
		wp_print_styles('social_connect');
    wp_print_styles('jquery-ui');
	} else {
		wp_enqueue_style("social_connect");
    wp_enqueue_style("jquery-ui");
	}
}
add_action('login_head', 'sc_add_stylesheets');

function sc_add_javascripts()
{
  if(!wp_script_is('social_connect', 'registered') ) {
    wp_register_script("social_connect", plugins_url() . "/wp_social_connect/media/js/connect.js");
  }

  // commented out check below as then the JS files are just not emitted, not sure why
//  if (did_action('wp_print_scripts')) {
    wp_print_scripts("jquery");
    wp_print_scripts('jquery-ui-core');
    wp_print_scripts('jquery-ui-dialog');
    wp_print_scripts("social_connect");
//	} else {
//    wp_enqueue_script("jquery");
//    wp_enqueue_script('jquery-ui-core');
//    wp_enqueue_script('jquery-ui-dialog');
//    wp_enqueue_script("social_connect");
//  }
}
add_action('login_head', 'sc_add_javascripts');

function sc_social_connect_admin_menu()
{
  add_options_page('Social Connect Settings', 'Social Connect', 'manage_options', 'social-connect-id', 'sc_render_social_connect_settings');
  add_action( 'admin_init', 'sc_register_social_connect_settings' );
}
add_action('admin_menu', 'sc_social_connect_admin_menu');

function sc_register_social_connect_settings()
{
	register_setting( 'social-connect-settings-group', 'social_connect_facebook_api_key' );
	register_setting( 'social-connect-settings-group', 'social_connect_facebook_secret_key' );
}


function sc_render_social_connect_settings()
{
?>
<div class="wrap">
<h2>Social Connect Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'social-connect-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Facebook API Key</th>
        <td><input type="text" name="social_connect_facebook_api_key" value="<?php echo get_option('social_connect_facebook_api_key'); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Facebook Secret Key</th>
        <td><input type="text" name="social_connect_facebook_secret_key" value="<?php echo get_option('social_connect_facebook_secret_key'); ?>" /></td>
        </tr>
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div> <?php
}

function sc_render_login_form_social_connect()
{
?>

<p>
  <a href="javascript://" class="social_connect_login">Social Connect</a>
</p>
<br/>
<div class="social_connect_form" title="Social Connect">
  <a href="#" class="socal_connect_login_facebook">Facebook</a> <br/>
  <a href="#" class="socal_connect_login_twitter">Twitter</a> <br/>
  <a href="#" class="socal_connect_login_google">Google</a> <br/>
  <a href="#" class="socal_connect_login_wordpress">WordPress</a> <br/>
</div>

<div class="social_connect_facebook_auth" client_id="<?php echo get_option('social_connect_facebook_api_key'); ?>" redirect_uri="<?php 
  echo urlencode(plugins_url() . '/wp_social_connect/facebook/callback.php'); ?>">
</div>


<?php
}
add_filter('login_form', 'sc_render_login_form_social_connect');

?>