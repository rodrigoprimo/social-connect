<?php
require(dirname(__FILE__) . '/openid.php');

try {
  if(!isset($_GET['openid_mode']) || $_GET['openid_mode'] == 'cancel') {
    $openid = new LightOpenID;
    $openid->identity = 'https://www.google.com/accounts/o8/id';
    $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');
    header('Location: ' . $openid->authUrl());
  } else {
    $openid = new LightOpenID;
    if($openid->validate()) {
      $google_id = $openid->identity;
      
      ?>
<html>
<head>
<script>
function init() {
  window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'google', 
    'social_connect_openid_identity' : '<?php echo $google_id ?>'});
    
  window.close();
}
</script>
</head>
<body onload="init();">
  <?php print_r($openid->getAttributes())?>
</body>
</html>      
      <?php
    }
  }
} catch(ErrorException $e) {
  echo $e->getMessage();
}
?>