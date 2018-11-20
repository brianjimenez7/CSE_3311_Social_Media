<?php
require_once __DIR__.'/vendor/autoload.php';
include('../classes/TESTLOGIN.php');
include('../classes/DB.php');
session_start();

$client = new Google_Client();
$client->setAuthConfigFile('config_youtube.json');
$client->setRedirectUri('http://localhost/monosmash/youtube'  . '/callback.php');
$client->addScope(Google_Service_YouTube::YOUTUBE_READONLY);
$client->setAccessType('offline');
$client->setApprovalPrompt('force');
$id = TESTLOGIN::isLoggedIn();

if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $access_token = $client->getAccessToken();
  DB::query('INSERT INTO youtube VALUES (:user_id, :access_token, :token_type, :expires_in, :refresh_token, :created)', array(':user_id'=>$id,':access_token'=>$access_token['access_token'], ':token_type'=>$access_token['token_type'],':expires_in'=>$access_token['expires_in'],':refresh_token'=>$access_token['refresh_token'], ':created'=>$access_token['created']));
  header('Location: http://localhost/monosmash/settings-social.php?fresh_connect=3');
}
?>