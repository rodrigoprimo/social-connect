<?php

function sc_render_login_form_social_connect()
{
  $images_url = SOCIAL_CONNECT_PLUGIN_URL . '/media/img/';
  
  $twitter_enabled = get_option('social_connect_twitter_enabled') && get_option('social_connect_twitter_consumer_key') && get_option('social_connect_twitter_consumer_secret');
  $facebook_enabled = get_option('social_connect_facebook_enabled', 1) && get_option('social_connect_facebook_api_key') && get_option('social_connect_facebook_secret_key');  
  $liveid_enabled = get_option('social_connect_liveid_enabled', 1) && get_option('social_connect_liveid_appid_key') && get_option('social_connect_liveid_secret_key') && get_option('social_connect_liveid_security_algorithm') && get_option('social_connect_liveid_return_url') && get_option('social_connect_liveid_policy_url');
  $google_enabled = get_option('social_connect_google_enabled', 1);
  $yahoo_enabled = get_option('social_connect_yahoo_enabled', 1);
  $openid_enabled = get_option('social_connect_openid_enabled', 1);
  $wordpress_enabled = get_option('social_connect_wordpress_enabled', 1);
?>

<div id="social_connect_ui">

<span><?php _e( 'Connect with:' ); ?></span><br/>
<div id="social_connect_form" class="social_connect_form" title="Social Connect">
  <?php if($facebook_enabled) { ?>
  <a href="javascript://" title="Facebook" class="social_connect_login_facebook"><img src="<?php echo $images_url . 'facebook_32.png' ?>" /></a>
  <?php } ?>
  <?php if($twitter_enabled) { ?>
    <a href="javascript://" title="Twitter" class="social_connect_login_twitter"><img src="<?php echo $images_url . 'twitter_32.png' ?>" /></a>
  <?php } ?>
  <?php if($liveid_enabled) { ?>
    <a href="javascript://" title="LiveID" class="social_connect_login_liveid"><img src="<?php echo $images_url . 'windows_32.png' ?>" /></a>
  <?php } ?>
  <?php if($google_enabled) { ?>
  <a href="javascript://" title="Google" class="social_connect_login_google"><img src="<?php echo $images_url . 'google_32.png' ?>" /></a>
  <?php } ?>
  <?php if($yahoo_enabled) { ?>
  <a href="javascript://" title="Yahoo" class="social_connect_login_yahoo"><img src="<?php echo $images_url . 'yahoo_32.png' ?>" /></a>
  <?php } ?>
  <?php if($openid_enabled) { ?>
  <a href="javascript://" title="OpenID" class="social_connect_login_openid"><img src="<?php echo $images_url . 'openid_32.png' ?>" /></a>
  <?php } ?>
  <?php if($wordpress_enabled) { ?>
  <a href="javascript://" title="WordPress" class="social_connect_login_wordpress"><img src="<?php echo $images_url . 'wordpress_32.png' ?>" /></a>
  <?php } ?>
</div>
<br />

<?php 
	$social_connect_provider = isset($_COOKIE['social_connect_current_provider']) ? $_COOKIE['social_connect_current_provider'] : '';
	$social_connect_user_name = isset($_COOKIE['social_connect_current_name']) ? $_COOKIE['social_connect_current_name'] : '';
	$social_connect_wordpress_blog_url = isset($_COOKIE['social_connect_wordpress_blog_url']) ? $_COOKIE['social_connect_wordpress_blog_url'] : '';
	$social_connect_openid_url = isset($_COOKIE['social_connect_openid_url']) ? $_COOKIE['social_connect_openid_url'] : '';
   
	
	if($social_connect_wordpress_blog_url == '') {
		$social_connect_wordpress_blog_name = '';
	} else {
		preg_match("/^(http:\/\/)?([^\/]+)/i", $social_connect_wordpress_blog_url, $matches);
		$host = $matches[2];  
		$subdomains = explode('.', $host);
		$social_connect_wordpress_blog_name = $subdomains[0];
	}

	if($social_connect_provider) {
		$social_connect_login_continue = 'social_connect_login_continue_' . $social_connect_provider;
		if($social_connect_provider == 'wordpress') {
			// trigger the wordpress URL form instead
			$social_connect_login_continue = 'social_connect_wordpress_proceed';
		}
?>
<div class="social_connect_already_connected_form" title="Social Connect" provider="<?php echo $social_connect_provider ?>">
  <img id="social_connect_already_connected_logo" src="<?php echo $images_url . $social_connect_provider . '_32.png' ?>" />
  <?php printf( __( 'Welcome back %s, %scontinue?%s', 'social_connect' ), $social_connect_user_name, '<a href="javascript://" class="'.$social_connect_login_continue.'">',  '</a>', 'social_connect' ); ?>
  
  <div style="clear:both;"></div>
  
  <br/>
  <a href="javascript://" class="social_connect_already_connected_form_not_you"><?php _e( 'Not you?', 'social_connect' ); ?></a> <br/>
  <a href="javascript://" class="social_connect_already_connected_user_another"><?php _e( 'Use another account', 'social_connect' ); ?></a> <br/>
</div>
<?php
	}
?>

<div class="social_connect_facebook_auth" client_id="<?php echo get_option('social_connect_facebook_api_key'); ?>" redirect_uri="<?php 
  echo urlencode(SOCIAL_CONNECT_PLUGIN_URL . '/facebook/callback.php'); ?>">
</div>

<div class="social_connect_twitter_auth" redirect_uri="<?php echo(SOCIAL_CONNECT_PLUGIN_URL . '/twitter/connect.php'); ?>">
</div>

<div class="social_connect_liveid_auth" appid="<?php echo get_option('social_connect_liveid_appid_key'); ?>" sec_algo="<?php echo get_option('social_connect_liveid_security_algorithm'); ?>" redirect_uri="<?php echo(SOCIAL_CONNECT_PLUGIN_URL . '/liveid/connect.php'); ?>">
</div>

<div class="social_connect_google_auth" redirect_uri="<?php echo(SOCIAL_CONNECT_PLUGIN_URL . '/google/connect.php'); ?>">
</div>

<div class="social_connect_yahoo_auth" redirect_uri="<?php echo(SOCIAL_CONNECT_PLUGIN_URL . '/yahoo/connect.php'); ?>">
</div>

<div class="social_connect_openid_auth" redirect_uri="<?php echo(SOCIAL_CONNECT_PLUGIN_URL . '/openid/connect.php'); ?>">
</div>

<div class="social_connect_wordpress_auth" redirect_uri="<?php echo(SOCIAL_CONNECT_PLUGIN_URL . '/wordpress/connect.php'); ?>">
</div>

<div class="social_connect_openid_form" title="OpenID">
  <p><?php _e( 'Enter your OpenID URL', 'social_connect' ); ?></p><br/>
  <p>
    <span>http://</span><input class="openid_url" size="15" value="<?php echo $social_connect_openid_url ?>"/> <br/><br/>
    <a href="javascript://" class="social_connect_openid_proceed"><?php _e( 'Proceed', 'social_connect' ); ?></a>
  </p>
</div>

<div class="social_connect_wordpress_form" title="WordPress">
  <p><?php _e( 'Enter your WordPress.com blog URL', 'social_connect' ); ?></p><br/>
  <p>
    <span>http://</span><input class="wordpress_blog_url" size="15" value="<?php echo $social_connect_wordpress_blog_name ?>"/><span>.wordpress.com</span> <br/><br/>
    <a href="javascript://" class="social_connect_wordpress_proceed"><?php _e( 'Proceed', 'social_connect' ); ?></a>
  </p>
</div>

</div> <!-- End of social_connect_ui div -->
<?php
}
add_action('login_form', 'sc_render_login_form_social_connect');
add_action('register_form', 'sc_render_login_form_social_connect');
add_action('after_signup_form', 'sc_render_login_form_social_connect');



function sc_social_connect_add_meta_to_comment_form()
{
  $social_connect_provider = isset($_COOKIE['social_connect_current_provider']) ? $_COOKIE['social_connect_current_provider'] : '';
  if($social_connect_provider != '') {
    echo "<input type='hidden' name='social_connect_comment_via_provider' value='$social_connect_provider' />";
  }
}

add_action('comment_form', 'sc_social_connect_add_meta_to_comment_form');




function sc_social_connect_add_comment_meta($comment_id) {
  $social_connect_comment_via_provider = isset($_POST['social_connect_comment_via_provider']) ? $_POST['social_connect_comment_via_provider'] : '';
  if($social_connect_comment_via_provider != '') {
  	update_comment_meta($comment_id, 'social_connect_comment_via_provider', $social_connect_comment_via_provider);
  }
}

add_action('comment_post', 'sc_social_connect_add_comment_meta');




function sc_social_connect_render_comment_meta($link) {
  global $comment;
  $images_url = SOCIAL_CONNECT_PLUGIN_URL . '/media/img/';
  $social_connect_comment_via_provider = get_comment_meta($comment->comment_ID, 'social_connect_comment_via_provider', true);
  if($social_connect_comment_via_provider) {
    return $link . "&nbsp;" . "<img id='social_connect_comment_via_provider' src='" . $images_url . $social_connect_comment_via_provider . "_16.png" . "' />";
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

add_action('comment_form', 'sc_render_comment_form_social_connect');



function sc_render_login_page_uri()
{
?>
  <div id="social_connect_login_form_uri" href="<?php echo site_url('wp-login.php', 'login_post'); ?>"></div>
<?php
}

add_action('wp_footer', 'sc_render_login_page_uri');



function sc_render_social_connect_widget($args)
{
	extract($args); // extracts before_widget,before_title,after_title,after_widget
	echo $before_widget . $before_title . $after_title;
  sc_render_login_form_social_connect();
  echo $after_widget;
}

function social_connect_plugin_loaded()
{
  $widget_ops = array('classname' => 'social_connect_widget', 'description' => "Allows users to register and login using their existing Twitter, Facebook, Windows Live ID, Google, Yahoo, OpenID and wordpress.com accounts" );
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
