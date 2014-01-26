<?php

require_once('twitteroauth/twitteroauth.php');

define('CONSUMER_KEY', get_option('social_connect_twitter_consumer_key'));
define('CONSUMER_SECRET', get_option('social_connect_twitter_consumer_secret'));
define( 'OAUTH_CALLBACK', home_url( 'index.php?social-connect=twitter-callback' ) );

if (CONSUMER_KEY != '' && CONSUMER_SECRET != '') {
	/* Build TwitterOAuth object with client credentials. */
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
 
	/* Get temporary credentials. */
	$request_token = $connection->getRequestToken(OAUTH_CALLBACK);
	$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	
	/* If last connection failed don't display authorization link. */
	switch ($connection->http_code) {
		case 200:
			/* Build authorize URL and redirect user to Twitter. */
			$url = $connection->getAuthorizeURL($token);
			wp_redirect($url);
			break;
		default:
			/* Show notification if something went wrong. */
			echo 'Could not connect to Twitter. Refresh the page or try again later.';
	}
  
	exit();
}
