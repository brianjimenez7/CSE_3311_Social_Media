<?php
require_once __DIR__.'/vendor/autoload.php';
$auth = include('./config_facebook.php');
if(!session_id()) {
    session_start();
}
$fb = new Facebook\Facebook([
  'app_id' => $auth['app_id'], // Replace {app-id} with your app id
  'app_secret' => $auth['app_secret'],
  'default_graph_version' => 'v2.11',
  ]);

$helper = $fb->getRedirectLoginHelper();
$_SESSION['FBRLH_state']=$_GET['state'];

$permissions = ['public_profile','user_likes']; // Optional permissions
$loginUrl = $helper->getLoginUrl('http://localhost/monosmash/facebook/facebook_callback.php', $permissions);
header("Location:". $loginUrl);
?>