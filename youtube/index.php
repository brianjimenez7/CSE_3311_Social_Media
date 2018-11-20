<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();
$client = new Google_Client();
$client->setAuthConfigFile('config_facebook.json');
$client->setRedirectUri('http://localhost/monosmash/youtube/callback.php');
$client->addScope(Google_Service_YouTube::YOUTUBE_READONLY);
$client->setAccessType('offline');

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
  $youtube = new Google_Service_YouTube($client);
  $channel = $youtube->subscriptions->listSubscriptions('snippet,contentDetails', array('mine' => true));
  print_r($channel);
} else {
  $redirect_uri = 'http://localhost/monosmash/youtube'  . '/callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
?>