<?php
require_once( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' );

$testing = "";
$msg = "";
$testing = $_REQUEST['testing'];
if ( $testing == "http://www.example.com" ) {
	$msg = __( 'Test was successful! <br/><br/> The rewrite rules on your server appear to be setup correctly for Social Connect to work.', 'social_connect' );
} else {
	$msg = sprintf( __( 'Test was unsuccessful. Expected "http://www.example.com", received "%s".', 'social_connect' ), $testing );
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
</head>
<body>
	<div class="wrap">
		<h2><?php _e( 'Social Connect Diagnostics', 'social_connect' ); ?></h2>
		<p><?php echo $msg; ?></p>
	</div>  
</body>
</html>


