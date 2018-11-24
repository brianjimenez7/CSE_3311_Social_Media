<?php
echo "hello";
require "vendor/autoload.php";
include('../classes/TESTLOGIN.php');
include('../classes/DB.php');
$auth = include('./config_facebook.php');

if(!session_id()) {
	session_start();
}
$id = TESTLOGIN::isLoggedIn();
$fb = new Facebook\Facebook([
  'app_id' => $auth['app_id'], // Replace {app-id} with your app id
  'app_secret' => $auth['app_secret'],
  'default_graph_version' => 'v2.12',
  ]);

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}

$oAuth2Client = $fb->getOAuth2Client();
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
$tokenMetadata->validateAppId('1578423715636972'); 
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
    exit;
	}
}
$user_token = $accessToken->getValue();
DB::query('INSERT INTO facebook VALUES (null,:id,:at)', array(':id'=>$id,':at'=>$user_token));
header('Location: https://741d1331.ngrok.io/monosmash/settings-social.php?fresh_connect=2');






// require "vendor/autoload.php";
// include('../classes/TESTLOGIN.php');
// include('../classes/DB.php');
// include('./config_facebook.php');

// if(!session_id()) {
// 	session_start();
// }
// $id = TESTLOGIN::isLoggedIn();
// $fb = new Facebook\Facebook([
//   'app_id' => $auth['app_id'], // Replace {app-id} with your app id
//   'app_secret' => $auth['app_secret'],
//   'default_graph_version' => 'v2.12',
//   ]);
  
// $helper = $fb->getRedirectLoginHelper();

// try {
//   $accessToken = $helper->getAccessToken();
// } catch(Facebook\Exceptions\FacebookResponseException $e) {
//   echo 'Graph returned an error: ' . $e->getMessage();
//   exit;
// } catch(Facebook\Exceptions\FacebookSDKException $e) {
//   echo 'Facebook SDK returned an error: ' . $e->getMessage();
//   exit;
// }

// if (! isset($accessToken)) {
//   if ($helper->getError()) {
//     header('HTTP/1.0 401 Unauthorized');
//     echo "Error: " . $helper->getError() . "\n";
//     echo "Error Code: " . $helper->getErrorCode() . "\n";
//     echo "Error Reason: " . $helper->getErrorReason() . "\n";
//     echo "Error Description: " . $helper->getErrorDescription() . "\n";
//   } else {
//     header('HTTP/1.0 400 Bad Request');
//     echo 'Bad request';
//   }
//   exit;
// }

// $oAuth2Client = $fb->getOAuth2Client();
// $tokenMetadata = $oAuth2Client->debugToken($accessToken);
// $tokenMetadata->validateAppId('1578423715636972'); 
// $tokenMetadata->validateExpiration();

// if (! $accessToken->isLongLived()) {
//   // Exchanges a short-lived access token for a long-lived one
//   try {
//     $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
//   } catch (Facebook\Exceptions\FacebookSDKException $e) {
//     echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
//     exit;
// 	}
// }
// $user_token = $accessToken->getValue();
// DB::query('INSERT INTO facebook VALUES (:id,:at)', array(':id'=>$id,':at'=>$user_token));
// header('Location: http://localhost/monosmash/settings-social.php?fresh_connect=2');
?> 