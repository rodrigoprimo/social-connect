<?php

require_once(dirname(dirname(__FILE__)) . '/openid/openid.php');
require_once(dirname(dirname(__FILE__)) . '/utils.php' );

try {
	if (!isset($_GET['openid_mode'])) {
		$openid = new LightOpenID;
		$openid->identity = urldecode($_GET['wordpress_blog_url']);
		$openid->required = array('namePerson', 'namePerson/friendly', 'contact/email');
		$openid->returnUrl = home_url('index.php?social-connect=wordpress');
		header('Location: ' . $openid->authUrl());
		die();
	} elseif($_GET['openid_mode'] == 'cancel') {
		?>
		<html>
			<body>
				<p><?php _e( 'You have cancelled this login. Please close this window and try again.', 'social-connect' ); ?></p>
			</body>
		</html>
		<?php
	} else {
		$openid = new LightOpenID;
		$openid->returnUrl = home_url('index.php?social-connect=wordpress');
		if ($openid->validate()) {
			$wordpress_id = $openid->identity;
			$attributes = $openid->getAttributes();
			$email = isset($attributes['contact/email']) ? $attributes['contact/email'] : '';
			$name = isset($attributes['namePerson']) ? $attributes['namePerson'] : '';
			$signature = social_connect_generate_signature($wordpress_id);
			if ($email == '') {
				?>
				<html>
					<body>
						<p><?php _e( 'You need to share your email address when prompted at wordpress.com. Please close this window and try again.', 'social-connect' ); ?></p>
					</body>
				</html>
				<?php
				die();
            }
            do_action( 'social_connect_before_register_wordpress', $wordpress_id, $signature );
			?>
			<html>
				<head>
					<script>
						function init() {
							window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'wordpress',
								'social_connect_signature' : '<?php echo $signature ?>',
								'social_connect_openid_identity' : '<?php echo $wordpress_id ?>',
								'social_connect_email' : '<?php echo $email ?>',
								'social_connect_name' : '<?php echo $name ?>'
							});
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
