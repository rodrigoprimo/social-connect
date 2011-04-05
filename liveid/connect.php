<?php
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php');
require_once(dirname(__FILE__) . '/windowslivelogin.php' );

// Specify true to log messages to Web server logs.
$DEBUG = false;

// Comma-delimited list of offers to be used.
$OFFERS = "Contacts.View";

// Name of cookie to use to cache the user token obtained through Web 
// Authentication. If a persistent cookie is being used, COOKIETTL 
// determines its expiry time.
$WEBAUTHCOOKIE = 'webauthtoken';
$COOKIETTL = time() + (10 * 365 * 24 * 60 * 60);

// The location of the Web Authentication control. You should not have 
// to change this value.
$CONTROLURL = 'http://login.live.com/controls/WebAuth.htm';

// The CSS style string to pass in to the Web Authentication control.
$CONTROLSTYLE = urlencode('font-size: 10pt; font-family: verdana; background: white;');

$liveid_appid	= get_option('social_connect_liveid_appid_key');
$liveid_secret	= get_option('social_connect_liveid_secret_key');
$liveid_secalgo	= 'wsignin1.0';
$liveid_return	= SOCIAL_CONNECT_PLUGIN_URL . '/liveid/callback.php';
$liveid_policy	= get_option('social_connect_liveid_policy_url');


$liveid_settings = array("appid"=>$liveid_appid,"secret"=>$liveid_secret,"securityalgorithm"=>$liveid_secalgo,"returnurl"=>$liveid_return,"policyurl"=>$liveid_policy);


if($liveid_appid && $liveid_secret && $liveid_secalgo) {
  // Initialize the WindowsLiveLogin module.
  $wll = WindowsLiveLogin::initFromXml(false,$liveid_settings);
  $wll->setDebug($DEBUG);
  $APPID = $wll->getAppId();

  $login_html = "<p>This application does not know who you are!  Click the <b>Sign in</b> link above.</p>";
  $consent_html = null;

  // If the user token obtained from sign-in through Web Authentication 
  // has been cached in a site cookie, attempt to process it and extract 
  // the user ID.
  $token = @$_COOKIE[$WEBAUTHCOOKIE];
  
  $userid = null;
  if ($token) {
      $user = $wll->processToken($token);
      if ($user) {
          $userid = $user->getId();
      }
  }

  // If the user ID is obtained successfully, prepare the message 
  // to include the consent URL if a valid token is not present in the 
  // persistent store; otherwise display the contents of the token.
  if ($userid) {
      $login_html = "<p>Now this application knows that you are the user with ID = \"<b>$userid</b>\".</p>";
    
      $consenturl = $wll->getConsentUrl($OFFERS);
      
      $consent_html = "<p>Please <a href=\"$consenturl\">click here</a> to grant consent so that we may access your Windows Live data.</p>";      
      
      // Attempt to get the raw consent token from persistent store for the 
      // current user ID.
      //$tokens  = new TokenDB($TOKENDB);
      //$token = $tokens->getToken($userid);
      $token = get_option($userid);
    
      $consenttoken = $wll->processConsentToken($token);
      
      if ($consenttoken) {
        if (!$consenttoken->isValid()) {      
            wp_redirect($consenturl);
        }
      }

    
      // If a consent token is found and is stale, try to refresh it and store  
      // it in persistent storage.
      if ($consenttoken) {
          if (!$consenttoken->isValid()) {
              if ($consenttoken->refresh() && $consenttoken->isValid()) {
                  //$tokens->setToken($userid, $consenttoken->getToken());
                  update_option($userid,$consenttoken->getToken());
              }
          }
    
          if ($consenttoken->isValid()) {
            // Convert Unix epoch time stamp to user-friendly format.
            $expiry = $consenttoken->getExpiry();
            $expiry = date(DATE_RFC2822, $expiry);
            //Prepare the message to display the consent token contents.
            $consent_html = <<<END
<p>Consent token found! The following are its contents..</p>

<table>
<tr><td>Delegation token</td><td>{$consenttoken->getDelegationToken()}</td></tr>
<tr><td>Location ID</td><td>{$consenttoken->getLocationID()}</td></tr>
<tr><td>Refresh token</td><td>{$consenttoken->getRefreshToken()}</td></tr>
<tr><td>Expiry</td><td>{$expiry}</td></tr>
<tr><td>Offers</td><td>{$consenttoken->getOffersString()}</td></tr>
<tr><td>Context</td><td>{$consenttoken->getContext()}</td></tr>
<tr><td>Token</td><td>{$consenttoken->getToken()}</td></tr>
</table>
END;
            
          }   
      }
  }

// This code embeds the Web Authentication control on your Web page 
// to display the appropriate application Sign in/Sign out link.
$control_html = <<<END
  <iframe 
    id="WebAuthControl" 
    name="WebAuthControl" 
    src="$CONTROLURL?appid=$APPID&style=$CONTROLSTYLE"
    width="80px"
    height="20px"
    marginwidth="0"
    marginheight="0"
    align="middle"
    frameborder="0"
    scrolling="no">
  </iframe>
END;
}

if ($consenttoken) {

  $dt = $consenttoken->getDelegationToken();  
  $header = array("Authorization: DelegatedToken dt=\"$dt\"\r\n");
  
  $opts = array(
    'http'=>array(
      'method'=>'GET',
      'header'=>$header
    )
  );

  $context = stream_context_create($opts);

  $xml = file_get_contents("https://livecontacts.services.live.com/users/@L@" . $consenttoken->getLocationID() . "/rest/livecontacts", false, $context); 

  if($xml) {
    $oSimpleXML = new SimpleXMLElement($xml);
    $contactsArray = array();
    $i = 0;
    $owner = $oSimpleXML->Owner;
    $liveid = $owner->WindowsLiveID;
    $email = $owner->Emails->Email->Address;
    if (trim($email) == "") {$email = $liveid;}
    $first_name = $owner->Profiles->Personal->FirstName;
    $last_name = $owner->Profiles->Personal->LastName;
    $signature = social_connect_generate_signature($liveid);
      
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script>
function init() {
  window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'liveid', 
    'social_connect_signature' : '<?php echo $signature ?>',
    'social_connect_liveid_identity' : '<?php echo $liveid ?>',
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

