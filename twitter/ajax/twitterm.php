<?php
include_once('./classes/TESTLOGIN.php');
include_once('./classes/DB.php');
require "./twitter/vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

$config = require './twitter/config_twitter.php';
$userid = TESTLOGIN::isLoggedIn();

class twitterm {
	private $config;
	private $userid;
	private $hasTwitter;
	private $credentials;
	private $twitterobj;
	public $embeds;

	function __construct ($Userid, $Config) {
		$this->userid = $Userid;
		$this->config = $Config;
	}
	private function hasTwitter() {
		if(DB::query('SELECT user_id FROM twitter WHERE user_id=:userid', array(':userid' => $this->userid))) {
			$this->hasTwitter = true;
			// //going to put this to see if works
			// return $this->hasTwitter;
		} else {
			throw new Exception("User Has No Facebook");
		}
	}
	private function LoadTwitterCredentials() {
		$sql_creds = DB::query('SELECT * FROM twitter WHERE user_id=:userid', array(':userid' => $this->userid));
		$tw_ot = $sql_creds[0]['oath_token'];
		$tw_ots = $sql_creds[0]['oath_token_secret'];
		// return $tw_ot;
		$this->credentials = array(
			"tw_ot" => $tw_ot,
			"tw_ots" => $tw_ots
		);
	}
	private function GetTwitterObject() {
		// connect with user token
		$this->twitterobj = new TwitterOAuth(
			$this->config['consumer_key'],
			$this->config['consumer_secret'],
			$this->credentials['tw_ot'],
			$this->credentials['tw_ots']
		);
	}
	private function GetTwitterEmbeds($count) {
		$embeds = [];
		$statustimelineparams = ['count' => $count, "trim_user" => false];
		$statustimeline = $this->twitterobj->get('statuses/home_timeline', $statustimelineparams);
		
		// return $this->twitterobj->getLastHttpCode();
		if ($this->twitterobj->getLastHttpCode() == 200) {
			// Tweet posted succesfully
			// return "able";
		} else {
				throw new Exception("Could not get Twitter Timeline");
		}
		foreach($statustimeline as $item) {
			$url = "https://twitter.com/" . $item->user->screen_name . "/status" . "/" . $item->id_str;
			$data = $this->twitterobj->get('statuses/oembed', ["url" => $url, 'omit_script' => 1]);
			if ($this->twitterobj->getLastHttpCode() == 200) {
				// Tweet posted succesfully
			} else {
					throw new Exception("Could not get Oembeds");
			}
			$embeds[] = array('created_time'=>$item->created_at, 'provider'=>'twitter', 'html'=>$data->html);
			$this->embeds = $embeds;
		}
		// return $embeds[0];
	}

	function connect() {
		try {
			$this->hasTwitter();
		}
		catch (Exception $e) {
			return 'Exception: '. $e->getMessage();
		}
		$this->LoadTwitterCredentials();
		$this->GetTwitterObject();
	}

	function userposts($limit) {
		try {
			$this->GetTwitterEmbeds($limit);
			echo $this->GetTwitterEmbeds($limit);
			
		}
		catch (Exception $e) {
			return 'Error: '. $e->getMessage();
		}
	}
}
?>