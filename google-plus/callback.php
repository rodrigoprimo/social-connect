<?php

require_once(dirname(dirname(__FILE__)) . '/constants.php' );

set_include_path(get_include_path() . PATH_SEPARATOR . plugin_dir_path(__FILE__));
require_once 'Google/Client.php';

if (isset($_GET['code'])) {
	$code = $_GET['code'];
	$client_id = get_option('social_connect_google_plus_client_id');
	$client_secret = get_option('social_connect_google_plus_client_secret');

	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);

	$client->authenticate($code);
	$token = json_decode($client->getAccessToken());

	$attributes = $client->verifyIdToken($token->id_token, $client_id)
		->getAttributes();
	$gplus_id = $attributes["payload"]["sub"];

	$signature = social_connect_generate_signature($access_token);

	?>
	<html>
	<head>
		<script>
			function init() {
				window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'google-plus',
					'social_connect_signature' : '<?php echo $signature ?>',
					'social_connect_access_token' : '<?php echo $access_token ?>'});

				window.close();
			}
		</script>
	</head>
	<body onload="init();"></body>
	</html>
<?php
}