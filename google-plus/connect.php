<?php

set_include_path(get_include_path() . PATH_SEPARATOR . plugin_dir_path(__FILE__));
require_once 'Google/Client.php';

$client_id = get_option('social_connect_google_plus_client_id');
$client_secret = get_option('social_connect_google_plus_client_secret');
$redirect_uri = SOCIAL_CONNECT_GOOGLE_PLUS_REDIRECT_URL;
$app_name = printf(__('Login to %s', 'social_connect'), get_option('siteurl'));

if (!empty($client_id) && !empty($client_secret)) {
	$client = new Google_Client();
	$client->setApplicationName($app_name);
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->addScope(array('email', 'profile'));

	$authUrl = $client->createAuthUrl();
	wp_redirect($authUrl);
	die();
}
