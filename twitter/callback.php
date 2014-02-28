<?php

require_once dirname( __FILE__ ) . '/../utils.php';

define('CONSUMER_KEY', get_option('social_connect_twitter_consumer_key'));
define('CONSUMER_SECRET', get_option('social_connect_twitter_consumer_secret'));

/**
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */
require_once('twitteroauth/twitteroauth.php');

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
	/* The user has been verified and the access tokens can be saved for future use */
	$_SESSION['status'] = 'verified';

	$user = $connection->get('account/verify_credentials');
	$name = $user->name;
	$screen_name = $user->screen_name;
	$twitter_id = $user->id;
	$signature = social_connect_generate_signature($twitter_id);

	do_action( 'social_connect_before_register_twitter', $twitter_id, $signature );
	?>
	
	<html>
		<head>
			<script>
				function init() {
					window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'twitter', 
						'social_connect_signature' : '<?php echo $signature ?>',
						'social_connect_twitter_identity' : '<?php echo $twitter_id ?>',
						'social_connect_screen_name' : '<?php echo $screen_name ?>',
						'social_connect_name' : '<?php echo $name ?>'});

					window.close();
				}
			</script>
		</head>
		<body onload="init();">
		</body>
	</html>
	
	<?php
} else {
	/* Save HTTP status for error dialog on connnect page.*/
	echo 'Login error';
}
