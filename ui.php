<?php

function sc_render_login_form_social_connect()
{
  $twitter_enabled = get_option('social_connect_twitter_enabled');
?>

<p>
  <a href="javascript://" class="social_connect_login">Social Connect</a>
</p>
<br/>
<div class="social_connect_form" title="Social Connect">
  <a href="#" class="social_connect_login_facebook">Facebook</a> <br/>
  <?php if($twitter_enabled) echo '<a href="#" class="social_connect_login_twitter">Twitter</a> <br/>'; ?>
  <a href="#" class="social_connect_login_google">Google</a> <br/>
  <a href="#" class="social_connect_login_wordpress">WordPress</a> <br/>
</div>

<?php 
$social_connect_provider = isset($_COOKIE['social_connect_current_provider']) ? $_COOKIE['social_connect_current_provider'] : '';
$social_connect_user_name = isset($_COOKIE['social_connect_current_name']) ? $_COOKIE['social_connect_current_name'] : '';
$social_connect_wordpress_blog_url = isset($_COOKIE['social_connect_wordpress_blog_url']) ? $_COOKIE['social_connect_wordpress_blog_url'] : '';

if($social_connect_provider) {
?>
<div class="social_connect_already_connected_form" title="Social Connect">
  Welcome back <?php echo $social_connect_user_name ?>, <a href="#" class="social_connect_login_<?php echo $social_connect_provider ?>">continue?</a> <br/><br/>
  <a href="#" class="social_connect_already_connected_form_not_you">Not you?</a> <br/><br/>
  <a href="#" class="social_connect_already_connected_user_another">Use another account</a> <br/>
</div>
<?php
}
?>

<div class="social_connect_facebook_auth" client_id="<?php echo get_option('social_connect_facebook_api_key'); ?>" redirect_uri="<?php 
  echo urlencode(plugins_url() . '/wp_social_connect/facebook/callback.php'); ?>">
</div>

<div class="social_connect_twitter_auth" redirect_uri="<?php echo(plugins_url() . '/wp_social_connect/twitter/connect.php'); ?>">
</div>

<div class="social_connect_google_auth" redirect_uri="<?php echo(plugins_url() . '/wp_social_connect/google/connect.php'); ?>">
</div>

<div class="social_connect_wordpress_auth" redirect_uri="<?php echo(plugins_url() . '/wp_social_connect/wordpress/connect.php'); ?>">
</div>

<div class="social_connect_wordpress_form" title="WordPress">
  <p>Enter your WordPress.com blog URL</p><br/>
  <p><input class="wordpress_blog_url" value="<?php echo $social_connect_wordpress_blog_url ?>"/> &nbsp; <a href="#" class="social_connect_wordpress_proceed">Proceed</a></p>
</div>

<?php
}
add_filter('login_form', 'sc_render_login_form_social_connect');
add_filter('register_form', 'sc_render_login_form_social_connect');
add_filter('after_signup_form', 'sc_render_login_form_social_connect');

function sc_render_comment_form_social_connect()
{
  if(comments_open() && !is_user_logged_in()) {
    sc_render_login_form_social_connect();
  }
}

add_filter('comment_form', 'sc_render_comment_form_social_connect');



function sc_render_login_page_uri()
{
?>
  <div id="social_connect_login_form_uri" href="<?php echo site_url('wp-login.php', 'login_post'); ?>"></div>
<?php
}

add_filter('wp_footer', 'sc_render_login_page_uri');

?>