<?php

require_once(dirname(dirname(__FILE__)) . '/openid/openid.php');
require_once(dirname(dirname(__FILE__)) . '/utils.php' );

try {
	if (!isset($_GET['openid_mode']) || $_GET['openid_mode'] == 'cancel') {
		$openid = new LightOpenID;
		$openid->identity = 'me.yahoo.com';
		$openid->required = array('namePerson', 'namePerson/friendly', 'contact/email');
		$openid->realm = home_url();
		$openid->returnUrl = home_url('index.php?social-connect=yahoo');
		header('Location: ' . $openid->authUrl());
		die();
	} else {
		$openid = new LightOpenID;
		$openid->returnUrl = home_url('index.php?social-connect=yahoo');
		if ($openid->validate()) {
			$yahoo_id = $openid->identity;
			$attributes = $openid->getAttributes();
			$email = $attributes['contact/email'];
			$name = $attributes['namePerson'];
			$username = $attributes['namePerson/friendly'];
			$signature = social_connect_generate_signature($yahoo_id);

			do_action( 'social_connect_before_register_yahoo', $yahoo_id, $signature );
			?>
			<html>
				<head>
					<script>
						function init() {
							window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'yahoo', 
								'social_connect_openid_identity' : '<?php echo $yahoo_id ?>',
								'social_connect_signature' : '<?php echo $signature ?>',
								'social_connect_email' : '<?php echo $email ?>',
								'social_connect_name' : '<?php echo $name ?>',
								'social_connect_username' : '<?php echo $username ?>'});
								
							window.close();
						}
					</script>
				</head>
				<body onload="init();"></body>
			</html>
			<?php
		}
	}
} catch(ErrorException $e) {
	echo $e->getMessage();
}
