<?php

require_once(dirname(dirname(__FILE__)) . '/constants.php' );
require_once(dirname(__FILE__) . '/facebook.php' );
require_once(dirname(dirname(__FILE__)) . '/utils.php' );

$client_id = get_option('social_connect_facebook_api_key');
$secret_key = get_option('social_connect_facebook_secret_key');

if (isset($_GET['code'])) {
	$code = $_GET['code'];
	$client_id = get_option('social_connect_facebook_api_key');
	$secret_key = get_option('social_connect_facebook_secret_key');

	parse_str( sc_http_get_contents( "https://graph.facebook.com/oauth/access_token?" .
		'client_id=' . $client_id . '&redirect_uri=' . home_url( 'index.php?social-connect=facebook-callback' ) .
		'&client_secret=' . $secret_key .
		'&code=' . urlencode( $code ) ) );

	$signature = social_connect_generate_signature($access_token);

	do_action( 'social_connect_before_register_facebook', $code, $signature, $access_token );
	?>
	<html>
		<head>
			<script>
				function init() {
					window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'facebook',
						'social_connect_signature' : '<?php echo $signature ?>',
						'social_connect_access_token' : '<?php echo $access_token ?>'});

					window.close();
				}
			</script>
		</head>
		<body onload="init();"></body>
	</html>
	<?php
} else {
	$redirect_uri = urlencode(SOCIAL_CONNECT_PLUGIN_URL . '/facebook/callback.php');
	wp_redirect('https://graph.facebook.com/oauth/authorize?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=email');
}
