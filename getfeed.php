<?php
include_once('/var/www/html/monosmash/facebook/ajax/facebookm.php');
include_once('/var/www/html/monosmash/twitter/ajax/twitterm.php');
include_once('/var/www/html/monosmash/classes/DB.php');
include_once('/var/www/html/monosmash/classes/TESTLOGIN.php');

$configfacebook = require '/var/www/html/monosmash/facebook/config_facebook.php';
$configtwitter = require '/var/www/html/monosmash/twitter/config_twitter.php';

$userid = TESTLOGIN::isLoggedIn();
$myfacebook = new facebookm($userid, $configfacebook);
$myfacebook->connect();
$myfacebook->userposts(5);

$mytwitter = new twitterm($userid, $configtwitter);
$mytwitter->connect();
$mytwitter->userposts(5);

$feed = array_merge($myfacebook->embeds, $mytwitter->embeds);
header('Content-Type: application/json');
echo json_encode($feed);

?>