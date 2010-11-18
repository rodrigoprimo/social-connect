<?php

function sc_render_login_form_social_connect()
{
  $images_url = plugins_url() . '/wp_social_connect/media/img/';
  
  $twitter_enabled = get_option('social_connect_twitter_enabled') && get_option('social_connect_twitter_consumer_key') && get_option('social_connect_twitter_consumer_secret');
  $facebook_enabled = get_option('social_connect_facebook_api_key') && get_option('social_connect_facebook_secret_key');
?>

<div id="social_connect_ui">

<span>Social Connect</span><br/>
<a href="javascript://" class="social_connect_login">
  <img title="Facebook" src="<?php echo $images_url . 'facebook.png' ?>" />
  <img title="Twitter" src="<?php echo $images_url . 'twitter.png' ?>" />
  <img title="Google" src="<?php echo $images_url . 'google.png' ?>" />
  <img title="WordPress" src="<?php echo $images_url . 'wordpress.png' ?>" />
</a>

<div id="social_connect_form" class="social_connect_form" title="Social Connect">
  <span>Please choose a provider to login</span><br/><br/>
  <?php if($facebook_enabled) { ?>
  <a href="javascript://" title="Facebook" class="social_connect_login_facebook"><img src="<?php echo $images_url . 'facebook.png' ?>" /></a>
  <?php } ?>
  <?php if($twitter_enabled) { ?>
    <a href="javascript://" title="Twitter" class="social_connect_login_twitter"><img src="<?php echo $images_url . 'twitter.png' ?>" /></a>
  <?php } ?>
  <a href="javascript://" title="Google" class="social_connect_login_google"><img src="<?php echo $images_url . 'google.png' ?>" /></a>
  <a href="javascript://" title="WordPress" class="social_connect_login_wordpress"><img src="<?php echo $images_url . 'wordpress.png' ?>" /></a>
</div>

<?php 
$social_connect_provider = isset($_COOKIE['social_connect_current_provider']) ? $_COOKIE['social_connect_current_provider'] : '';
$social_connect_user_name = isset($_COOKIE['social_connect_current_name']) ? $_COOKIE['social_connect_current_name'] : '';
$social_connect_wordpress_blog_url = isset($_COOKIE['social_connect_wordpress_blog_url']) ? $_COOKIE['social_connect_wordpress_blog_url'] : '';

if($social_connect_wordpress_blog_url == '') {
  $social_connect_wordpress_blog_name = '';
} else {
  preg_match("/^(http:\/\/)?([^\/]+)/i", $social_connect_wordpress_blog_url, $matches);
  $host = $matches[2];  
  $subdomains = explode('.', $host);
  $social_connect_wordpress_blog_name = $subdomains[0];
}


if($social_connect_provider) {
?>
<div class="social_connect_already_connected_form" title="Social Connect">
  <img id="social_connect_already_connected_logo" src="<?php echo $images_url . $social_connect_provider . '.png' ?>" />
  Welcome back <?php echo $social_connect_user_name ?>, <a href="javascript://" class="social_connect_login_<?php echo $social_connect_provider ?>">continue?</a>
  
  <div style="clear:both;"></div>
  
  <br/>
  <a href="javascript://" class="social_connect_already_connected_form_not_you">Not you?</a> <br/>
  <a href="javascript://" class="social_connect_already_connected_user_another">Use another account</a> <br/>
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
  <p>
    <span>http://</span><input class="wordpress_blog_url" size="15" value="<?php echo $social_connect_wordpress_blog_name ?>"/><span>.wordpress.com</span> <br/><br/>
    <a href="javascript://" class="social_connect_wordpress_proceed">Proceed</a>
  </p>
</div>

</div> <!-- End of social_connect_ui div -->
<?php
}
add_filter('login_form', 'sc_render_login_form_social_connect');
add_filter('register_form', 'sc_render_login_form_social_connect');
add_filter('after_signup_form', 'sc_render_login_form_social_connect');



function sc_social_connect_add_meta_to_comment_form()
{
  $social_connect_provider = isset($_COOKIE['social_connect_current_provider']) ? $_COOKIE['social_connect_current_provider'] : '';
  if($social_connect_provider != '') {
    echo "<input type='hidden' name='social_connect_comment_via_provider' value='$social_connect_provider' />";
  }
}

add_filter('comment_form', 'sc_social_connect_add_meta_to_comment_form');




function sc_social_connect_add_comment_meta($comment_id) {
  $social_connect_comment_via_provider = isset($_POST['social_connect_comment_via_provider']) ? $_POST['social_connect_comment_via_provider'] : '';
  if($social_connect_comment_via_provider != '') {
  	update_comment_meta($comment_id, 'social_connect_comment_via_provider', $social_connect_comment_via_provider);
  }
}

add_action ('comment_post', 'sc_social_connect_add_comment_meta');




function sc_social_connect_render_comment_meta($link) {
  global $comment;
  $images_url = plugins_url() . '/wp_social_connect/media/img/';
  $social_connect_comment_via_provider = get_comment_meta($comment->comment_ID, 'social_connect_comment_via_provider', true);
  if($social_connect_comment_via_provider) {
    return $link . "&nbsp;" . "<img id='social_connect_comment_via_provider' src='" . $images_url . $social_connect_comment_via_provider . "_small.png" . "' />";
  } else {
    return $link;
  }
}

add_action ('get_comment_author_link', 'sc_social_connect_render_comment_meta');




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



function sc_render_social_connect_widget($args)
{
	extract($args); // extracts before_widget,before_title,after_title,after_widget
	echo $before_widget . $before_title . $after_title;
  sc_render_login_form_social_connect();
  echo $after_widget;
}

function social_connect_plugin_loaded()
{
  $widget_ops = array('classname' => 'social_connect_widget', 'description' => "Allows users to register and login using their existing Twitter, Facebook, Google and wordpress.com accounts" );
  wp_register_sidebar_widget('social_connect_widget', 'Social Connect', 'sc_render_social_connect_widget', $widget_ops);
}

add_action('plugins_loaded','social_connect_plugin_loaded');


function sc_social_connect_shortcode_handler($args)
{
  if(!is_user_logged_in()) {
    sc_render_login_form_social_connect();
  }
}

add_shortcode('social_connect', 'sc_social_connect_shortcode_handler');

?>