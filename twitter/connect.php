<?php
require(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php');
require(dirname(__FILE__) . '/EpiCurl.php' );
require(dirname(__FILE__) . '/EpiOAuth.php' );
require(dirname(__FILE__) . '/EpiTwitter.php' );

$twitter_enabled = get_option('social_connect_twitter_enabled');
$consumer_key = get_option('social_connect_twitter_consumer_key');
$consumer_secret = get_option('social_connect_twitter_consumer_secret');

if($twitter_enabled && $consumer_key && $consumer_secret) {
  $twitter_api = new EpiTwitter($consumer_key, $consumer_secret);
  wp_redirect($twitter_api->getAuthenticateUrl());
  exit();
}
?>
<p>Social Connect plugin has not been configured for Twitter</p>

