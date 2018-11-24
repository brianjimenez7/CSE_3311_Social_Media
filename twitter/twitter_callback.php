<?php
	// echo "here";
	include('../classes/TESTLOGIN.php');
	include('../classes/DB.php');
	session_start();

	require "./vendor/autoload.php";
	use Abraham\TwitterOAuth\TwitterOAuth;
	//echo "here";
	$config = include ('./config_twitter.php');
	// echo $config['consumer_key'];
	$id = TESTLOGIN::isLoggedIn();
	$oauth_verifier = filter_input(INPUT_GET, 'oauth_verifier');
	
	if (empty($oauth_verifier) || empty($_SESSION['oauth_token']) || empty($_SESSION['oauth_token_secret'])) {
		// something's missing, go and login again
		echo "if ";
		die('no oauth verifier or oauth token or oauth otken secret');
		
	} else {
		echo "else <br>";
		//echo $config['consumer_secret']; 
		$connection = new TwitterOAuth(
			$config['consumer_key'],
			$config['consumer_secret'],
			$_SESSION['oauth_token'],
			$_SESSION['oauth_token_secret']
		);
		//echo $connection;
		// //echo $connection;
		$token = $connection->oauth('oauth/access_token', array("oauth_verifier" => $oauth_verifier));
		$ot = $token['oauth_token'];
		$ots = $token['oauth_token_secret'];
		// echo $id;
		// echo "<br>";
		// echo $ot;
		// echo "<br>";
		// echo $ots;
		// echo "<br>";
		DB::query('INSERT INTO twitter VALUES (null,:id,:ot,:ots)', array(':id'=>$id,':ot'=>$ot,':ots'=>$ots));
		header('Location: https://741d1331.ngrok.io/monosmash/settings-social.php?fresh_connect=1');
		//header('Location: http://localhost:8888/monosmash/home.php');
		// header('Location: home.php');
	}
	
?>