<?php
	include('../classes/TESTLOGIN.php');
	include('../classes/DB.php');
	session_start();

	require "/var/www/html/monosmash/twitter/vendor/autoload.php";
	use Abraham\TwitterOAuth\TwitterOAuth;

	$config = require '/var/www/html/monosmash/twitter/config_twitter.php';
	$id = TESTLOGIN::isLoggedIn();
	$oauth_verifier = filter_input(INPUT_GET, 'oauth_verifier');
	
	if (empty($oauth_verifier) || empty($_SESSION['oauth_token']) || empty($_SESSION['oauth_token_secret'])) {
		// something's missing, go and login again
		die('no oauth verifier or oauth token or oauth otken secret');
	} else {
		$connection = new TwitterOAuth(
			$config['consumer_key'],
			$config['consumer_secret'],
			$_SESSION['oauth_token'],
			$_SESSION['oauth_token_secret']
		);
		$token = $connection->oauth('oauth/access_token', array("oauth_verifier" => $oauth_verifier));
		$ot = $token['oauth_token'];
		$ots = $token['oauth_token_secret'];
		DB::query('INSERT INTO twitter VALUES (:id,:ot,:ots)', array(':id'=>$id,':ot'=>$ot,':ots'=>$ots));
		header('Location: http://localhost/monosmash/settings-social.php?fresh_connect=1');
	}
?>