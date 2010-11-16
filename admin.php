<?php

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
  register_setting( 'social-connect-settings-group', 'social_connect_twitter_enabled');
  register_setting( 'social-connect-settings-group', 'social_connect_twitter_consumer_key');
  register_setting( 'social-connect-settings-group', 'social_connect_twitter_consumer_secret');
}


function sc_render_social_connect_settings()
{
?>
<div class="wrap">
<h2>Social Connect Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'social-connect-settings-group' ); ?>

    <h3>Facebook Settings</h3>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">API Key</th>
        <td><input type="text" name="social_connect_facebook_api_key" value="<?php echo get_option('social_connect_facebook_api_key'); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Secret Key</th>
        <td><input type="text" name="social_connect_facebook_secret_key" value="<?php echo get_option('social_connect_facebook_secret_key'); ?>" /></td>
        </tr>
    </table>

    <br/><br/>

    <h3>Twitter Settings</h3>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Enable?</th>
        <td>
          <input type="checkbox" name="social_connect_twitter_enabled" value="1" <?php checked(get_option('social_connect_twitter_enabled'), 1); ?> /><br/>
          Twitter integration requires the generation of dummy email addresses for authenticating users. <br/>
          Please check with your domain administrator as this may require changes to your mail server. <br/><br/>
          When configuring your Twitter application, ensure that the callback URL is <br/>
          <?php echo plugins_url() . '/wp_social_connect/twitter/callback.php'; ?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Consumer Key</th>
        <td><input type="text" name="social_connect_twitter_consumer_key" value="<?php echo get_option('social_connect_twitter_consumer_key'); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Consumer Secret</th>
        <td><input type="text" name="social_connect_twitter_consumer_secret" value="<?php echo get_option('social_connect_twitter_consumer_secret'); ?>" /></td>
        </tr>
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div> <?php
}
