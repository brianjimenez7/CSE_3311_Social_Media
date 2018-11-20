<?php
	require "vendor/autoload.php";
	use Abraham\TwitterOAuth\TwitterOAuth;
	
	session_start();
	$config = require_once 'config_twitter.php';

		function genAuthToken($config) {
			global $twitteroauth;
			global $request_token;
			global $config;
			$twitteroauth = new TwitterOAuth($config['consumer_key'], $config['consumer_secret']);
			$request_token = $twitteroauth->oauth('oauth/request_token', array('oauth_callback' => $config['url_callback']));
			if($twitteroauth->getLastHttpCode() != 200) {
				die('There was a problem performing this request');
			}
		}
	
		function setTokGenURL() {
			global $twitteroauth;
			global $request_token;
			$_SESSION['oauth_token'] = $request_token['oauth_token'];
			$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
			$url = $twitteroauth->url('oauth/authorize', ['oauth_token' => $request_token['oauth_token']]);
			return $url;
		}
	
		genAuthToken();
		$url = setTokGenURL();
		header('Location: '. $url);
?>