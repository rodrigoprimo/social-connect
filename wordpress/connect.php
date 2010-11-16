<?php
require(dirname(dirname(__FILE__)) . '/openid/openid.php');

try {
  if(!isset($_GET['openid_mode'])) {
    $openid = new LightOpenID;
    $openid->identity = urldecode($_GET['wordpress_blog_url']);
    $openid->required = array('namePerson', 'namePerson/friendly', 'contact/email');
    header('Location: ' . $openid->authUrl());
  } elseif($_GET['openid_mode'] == 'cancel') {
    ?>
    <html>
    <body>
      <p>You need to share your email address when prompted at wordpress.com. Please close this window and try again.</p>
    </body>
    </html>
    <?php
  } else {
    $openid = new LightOpenID;
    if($openid->validate()) {
      $wordpress_id = $openid->identity;
      $attributes = $openid->getAttributes();
      $email = isset($attributes['contact/email']) ? $attributes['contact/email'] : '';
      $first_name = isset($attributes['namePerson']) ? $attributes['namePerson'] : '';
      $last_name = '';
      
      if($email == '') {
        ?>
        <html>
        <body>
          <p>You need to share your email address when prompted at wordpress.com. Please close this window and try again.</p>
        </body>
        </html>
        <?php
        die();
      }
      
      ?>
<html>
<head>
<script>
function init() {
  window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'wordpress', 
    'social_connect_openid_identity' : '<?php echo $wordpress_id ?>',
    'social_connect_email' : '<?php echo $email ?>',
    'social_connect_first_name' : '<?php echo $first_name ?>',
    'social_connect_last_name' : '<?php echo $last_name ?>'});
    
  window.close();
}
</script>
</head>
<body onload="init();">
</body>
</html>      
      <?php
    }
  }
} catch(ErrorException $e) {
  echo $e->getMessage();
}
?>