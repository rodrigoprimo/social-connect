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


// URL of Web Authentication sample index page.
$INDEX = 'connect.php';

// Landing pages to use after processing login and logout respectively.
$LOGIN = $INDEX;
$LOGOUT = $INDEX;

$liveid_appid = get_option('social_connect_liveid_appid_key');
$liveid_secret = get_option('social_connect_liveid_secret_key');
$liveid_secalgo = "wsignin1.0";
$liveid_return = SOCIAL_CONNECT_PLUGIN_URL . '/liveid/callback.php';
$liveid_policy = get_option('social_connect_liveid_policy_url');

$liveid_settings = array("appid"=>$liveid_appid,"secret"=>$liveid_secret,"securityalgorithm"=>$liveid_secalgo,"returnurl"=>$liveid_return,"policyurl"=>$liveid_policy);

// Initialize the WindowsLiveLogin module.
$wll = WindowsLiveLogin::initFromXml(false,$liveid_settings);
$wll->setDebug($DEBUG);

// Extract the 'action' parameter, if any, from the request.
$action = @$_REQUEST['action'];

// If action is 'logout', clear the login cookie and redirect to the
// logout page.
//
// If action is 'clearcookie', clear the login cookie and return a GIF
// as a response to signify success.
//
// If action is 'login', try to process sign-in. If the sign-in is  
// successful, cache the user token in a cookie and redirect to the  
// site's main page. If sign-in failed, clear the cookie and redirect 
// to the main page.
//
// If action is 'delauth', get user token from the cookie. Process the 
// consent token. If the consent token is valid, store the raw consent 
// token in persistent storage. Redirect to the site's main page.
switch ($action) {
    case 'logout':
        setcookie($WEBAUTHCOOKIE);
        header("Location: $LOGOUT");
        break;
    case "clearcookie":
        ob_start();
        setcookie($WEBAUTHCOOKIE);

        list($type, $response) = $wll->getClearCookieResponse();
        header("Content-Type: $type");
        print($response);

        ob_end_flush();
        break;
    case 'login':
        $user = $wll->processLogin($_REQUEST);
      
        if ($user) {
            if ($user->usePersistentCookie()) {
                setcookie($WEBAUTHCOOKIE, $user->getToken(), $COOKIETTL);
            }
            else {
                setcookie($WEBAUTHCOOKIE, $user->getToken());
            }
            header("Location: $LOGIN");
        }
        else {
            setcookie($WEBAUTHCOOKIE);
            header("Location: $LOGIN");
        }
        break;
    case 'delauth':
    $user = null;
    $logintoken = @$_COOKIE[$WEBAUTHCOOKIE];

    if ($logintoken) {
      $user = $wll->processToken($logintoken);
    }

    if ($user) {
      $consent = $wll->processConsent(@$_REQUEST);
      if ($consent && $consent->isValid()) {
        //$tokens  = new TokenDB($TOKENDB);
        //$tokens->setToken($user->getId(), $consent->getToken());  
        update_option($user->getId(),$consent->getToken());        
      } else {
        update_option($user->getId(),""); 
      }
    }
        header("Location: $LOGIN");
        break;
    default:
        header("Location: $LOGIN");
        break;
}
?>
