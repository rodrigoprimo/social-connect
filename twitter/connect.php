<?php
require(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php');
require(dirname(__FILE__) . '/EpiCurl.php' );
require(dirname(__FILE__) . '/EpiOAuth.php' );
require(dirname(__FILE__) . '/EpiTwitter.php' );

$consumer_key = get_option('social_connect_twitter_consumer_key');
$consumer_secret = get_option('social_connect_twitter_consumer_secret');
$twitter_api = new EpiTwitter($consumer_key, $consumer_secret);

wp_redirect($twitter_api->getAuthenticateUrl());

?>