<?php
include_once('./classes/DB.php');
include_once('./classes/TESTLOGIN.php');
require "./facebook/vendor/autoload.php";

$config = require './facebook/config_facebook.php';
$userid = TESTLOGIN::isLoggedIn();

class facebookm {
	private $config;
	private $userid;
	private $hasFacebook;
	private $credentials;
	private $facebookobj;
	private $posts;
	public $embeds;

	function __construct ($Userid, $Config) {
		$this->userid = $Userid;
		$this->config = $Config;
	}
	private function hasFacebook() {
		if(DB::query('SELECT user_id FROM facebook WHERE user_id=:userid', array(':userid' => $this->userid))) {
			//goes in here
			$this->hasFacebook = true;
		} else {
			throw new Exception("User Has No Facebook");
		}
	}
	private function LoadFacebookCredentials() {
		$sql_creds = DB::query('SELECT * FROM facebook WHERE user_id=:userid', array(':userid' => $this->userid));
		// $fb_at = $sql_creds[0]['fb_access_token'];
		$fb_at = $sql_creds[0]['user_token']; 
		// return $fb_at; 
		$this->credentials = array(
			"fb_at" => $fb_at
		);
	}
	private function GetFacebookObject() {
		// connect with user token
		$this->facebookobj = new Facebook\Facebook([
			'app_id' => $this->config['app_id'],
			'app_secret' => $this->config['app_secret']
		]);
	}
	private function GetPostsURL() {
		try {
			$posts = [];
			//ndsj=/me/likes?fields=id,name
			$response = $this->facebookobj->get('me?fields=id,name,posts', $this->credentials['fb_at']);
			$graphEdge = $response->getGraphEdge();
			foreach($graphEdge as $graphNode) {
				$pageposts = $this->facebookobj->get('/' . $graphNode['id'] . '/posts?limit=1&fields=message,created_time,description', $this->credentials['fb_at']);
				$edge = $pageposts->getGraphEdge();
				foreach($edge as $node) {
					$posts[] = array('created_time'=>$node['created_time']->format('Y-m-d H:i:s'), 'provider'=>'facebook', 'message'=>$node['message']);
				}
			}
			$this->posts = $posts;
			return $post[0];

		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			return 'Graph returned an error: at GetPostsURL top level' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			return 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
	}
	private function GetEmbeds() {
		$embeds = [];
		$oembed_endpoint = 'https://www.facebook.com/plugins/post/oembed';
		foreach($this->posts as $post) {
			$json_url = $oembed_endpoint . '.json/?url=' . rawurlencode($post['post_url']) . '&maxwidth=550&omitscript=1';
			$curl = curl_init($json_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
			$result = curl_exec($curl);
			$jsonr = json_decode($result, true);
			$embeds[] = array('created_time'=>$post['created_time'], 'provider'=>$post['provider'], 'html'=>$jsonr['html']);
			curl_close($curl);
		}
		$this->embeds = $embeds;
	}
	function connect() {
		try {
			$this->hasFacebook();
		} 
		catch (Exception $e) {
			return 'Exception:' .$e->getMessage();
		}
		$this->LoadFacebookCredentials();
		$this->GetFacebookObject();
		
		// return $this->LoadFacebookCredentials();
	}
	function userposts($limit) {
		try {
			return $this->GetPostsURL();
		}
		catch (Exception $e) {
			return 'Exception:' .$e->getMessage();
		}
		// $this->GetEmbeds();
	}
}
?>
