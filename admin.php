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
	<p>To connect your site to Facebook, you need a Facebook Application. If you have already created one, please insert your API & Secret key below.</p>
	<p>Already registered? Find your keys in your <a target="_blank" href="http://www.facebook.com/developers/apps.php">Facebook Application List</a></li>
	<p>Need to register?</p>
	<ol>
	<li>Visit the <a target="_blank" href="http://www.facebook.com/developers/createapp.php">Facebook Application Setup</a> page</li>
	<li>Get the API information from the <a target="_blank" href="http://www.facebook.com/developers/apps.php">Facebook Application List</a></li>
	<li>Select the application you created, then copy and paste the API key & Application Secret from there.</li>
	</ol>
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

    <h3>Twitter Settings</h3>
	<p>To offer login via Twitter, you need to register your site as a Twitter Application and get a <strong>Consumer Key</strong>, a <strong>Consumer Secret</strong>, an <strong>Access Token</strong> and an <strong>Access Token Secret</strong>.</p>
	<p>Already registered? Find your keys in your <a href="http://dev.twitter.com/apps">Twitter Application List</a></p>
	<p>Need to register? <a href="http://dev.twitter.com/apps/new">Register an Application</a> and fill the form with the details below:
	<ol>
		<li>Application Type: <strong>Browser</strong></li>
		<li>Callback URL: <strong><?php echo plugins_url() . '/wp_social_connect/twitter/callback.php'; ?></strong></li>
		<li>Default Access: <strong>Read &amp; Write</strong></li>
	</ol>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Enable?</th>
        <td>
          <input type="checkbox" name="social_connect_twitter_enabled" value="1" <?php checked(get_option('social_connect_twitter_enabled'), 1); ?> /><br/>
          Twitter integration requires the generation of dummy email addresses for authenticating users. <br/>
          Please check with your domain administrator as this may require changes to your mail server.
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
