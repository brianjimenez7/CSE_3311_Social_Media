<?php
include_once('./facebook/ajax/facebookm.php');
include_once('./twitter/ajax/twitterm.php');
include_once('./classes/DB.php');
include_once('./classes/TESTLOGIN.php');

$configfacebook = require './facebook/config_facebook.php';
$configtwitter = require './twitter/config_twitter.php';

$userid = TESTLOGIN::isLoggedIn();
$myfacebook = new facebookm($userid, $configfacebook);
$myfacebook->connect();
$myfacebook->userposts(5);

$mytwitter = new twitterm($userid, $configtwitter);
$mytwitter->connect();
$mytwitter->userposts(5);

$feed = array_merge($myfacebook->embeds, $mytwitter->embeds);
$feed=$myfacebook->embeds;
//$feed=$mytwitter->embeds;
header('Content-Type: application/json');
echo json_encode($feed);

?>