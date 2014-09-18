<?php

require_once(dirname(dirname(__FILE__)) . '/constants.php' );

set_include_path(get_include_path() . PATH_SEPARATOR . plugin_dir_path(__FILE__));
require_once 'Google/Client.php';
require_once 'Google/Service/Oauth2.php';

if (isset($_GET['code'])) {
	$code = $_GET['code'];
	$client_id = get_option('social_connect_google_plus_client_id');
	$client_secret = get_option('social_connect_google_plus_client_secret');
	$redirect_uri = SOCIAL_CONNECT_GOOGLE_PLUS_REDIRECT_URL;

	$client = new Google_Client();
	$client->setClientId( $client_id );
	$client->setClientSecret( $client_secret );
	$client->setRedirectUri( $redirect_uri );

	if ( isset( $_REQUEST['logout'] ) ) {
		unset( $_SESSION['access_token'] );
	}

	if ( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) {
		$client->setAccessToken( $_SESSION['access_token'] );
	} elseif ( isset( $code ) ) {
		$client->authenticate( $_GET['code'] );
		$_SESSION['access_token'] = $client->getAccessToken();
	}

	$token = json_decode( $client->getAccessToken() );

	$google_oauthV2 = new Google_Service_Oauth2( $client );
	$user = $google_oauthV2->userinfo->get();
	$google_id = $user['id'];
	$email = $user['email'];
	$first_name = $user['givenName'];
	$last_name = $user['familyName'];
	$profile_url = $user['link'];


	$signature = social_connect_generate_signature( $google_id );

	?>
	<html>
	<head>
		<script>
			function init() {
				window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'google-plus',
					'social_connect_signature' : '<?php echo $signature ?>',
					'social_connect_google_id' : '<?php echo $google_id ?>',
					'social_connect_email' : '<?php echo $email ?>',
					'social_connect_first_name' : '<?php echo $first_name ?>',
					'social_connect_last_name' : '<?php echo $last_name ?>',
					'social_connect_profile_url' : '<?php echo $profile_url ?>'});

				window.close();
			}
		</script>
	</head>
	<body onload="init();"></body>
	</html>
<?php
}