<?php
include('/var/www/html/monosmash/classes/DB.php');
include('/var/www/html/monosmash/classes/TESTLOGIN.php');
require "/var/www/html/monosmash/youtube/vendor/autoload.php";
session_start();
$userid = TESTLOGIN::isLoggedIn();

function hasYoutube() {
	global $userid;
	if(DB::query('SELECT user_id FROM youtube WHERE user_id=:userid', array(':userid' => $userid))) {
		return true;
	} else {
		return false;
	}
}
function LoadYoutubeCredentials() {
	global $userid;
	$sql_creds = DB::query('SELECT * FROM youtube WHERE user_id=:userid', array(':userid' => $userid));
	$access_token = array(
		'access_token' => $sql_creds[0]['access_token'],
		'token_type' => $sql_creds[0]['token_type'],
		'expires_in' => $sql_creds[0]['expires_in'],
		'refresh_token' => $sql_creds[0]['refresh_token'],
		'created' => $sql_creds[0]['created']
	);
	return $access_token;
}
function GetYoutubeService($access_token) {
	global $userid;
	$client = new Google_Client();
	$client->setAuthConfigFile('/var/www/html/monosmash/youtube/config_youtube.json');
	$client->setRedirectUri('http://localhost/monosmash/youtube/callback.php');
	$client->addScope(Google_Service_YouTube::YOUTUBE_READONLY);
	$client->setAccessType('offline');
	$client->setAccessToken($access_token);
 	if($client->isAccessTokenExpired()) {
		$client->refreshToken($client->getRefreshToken());
		$new_access_token = $client->getAccessToken();
		DB::query('REPLACE INTO youtube VALUES (user_id,access_token,token_type,expires_in,refresh_token,created)', array('user_id'=>$userid, 'access_token'=>$new_access_token['access_token'], 'token_type'=>$new_access_token['token_type'],'expires_in'=>$new_access_token['expires_in'],'refresh_token'=>$new_access_token['refresh_token'], 'created'=>$new_access_token['created']));
	}
	$service = new Google_Service_YouTube($client);
	return $service;
}
function GetChannelIds($youtube) {
	$channelIds = [];
	$channels = $youtube->subscriptions->listSubscriptions('snippet', array('mine' => true, 'maxResults'=>50));
//for now comment off to only make 3 request later uncomment and set 3 to 50
 if($channels['nextPageToken']) {
		while($channels['nextPageToken']) {
			foreach ($channels['items'] as $channel) {
				$channelIds[] = $channel['snippet']['resourceId']['channelId'];
			}
			$channels = $youtube->subscriptions->listSubscriptions('snippet', array('mine' => true, 'maxResults'=>50, 'pageToken' => $channels['nextPageToken']));
		}
		foreach ($channels['items'] as $channel) {
			$channelIds[] = $channel['snippet']['resourceId']['channelId'];
		}
	}
	else {
		foreach ($channels['items'] as $channel) {
			$channelIds[] = $channel['snippet']['resourceId']['channelId'];
		}
	}
	return $channelIds;
}
function GetChannelsPlaylist($youtube, $channelIds) {
	$channelsPlaylist = [];

	foreach($channelIds as $channel) {
		$response = $youtube->channels->listChannels('contentDetails', array('id'=>$channel));
		//print_r($response);
		$channelsPlaylist[] = $response['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
	}
	return $channelsPlaylist;
}
function GetPlaylistUploads($youtube, $channelsPlaylist) {
	$videos = [];
	foreach($channelsPlaylist as $playlistId) {
		$channelData = [];
		$response = $youtube->playlistItems->listPlaylistItems('snippet', array('maxResults' => 3,'playlistId'=>$playlistId));
		foreach($response['items'] as $upload) {
			$channelData['name'] = $upload['snippet']['channelTitle'];
			$channelData['title'] = $upload['snippet']['title'];
			$channelData['publishedAt'] = $upload['snippet']['publishedAt'];
			$channelData['videoId'] = $upload['snippet']['resourceId']['videoId'];
			$videos[] = $channelData;
		}
	}
	return $videos;
}
function BuildEmbeds($videos) {
	$embeds = [];

	foreach($videos as $video) {
		$htmlCardImg = '<div class="card" style="width: 28rem;"><div class="card-img-top img-fluid">
			                  <div class="embed-responsive embed-responsive-16by9">
													<iframe class="embed-responsive-item" width="560" height="315" 
														 src="https://www.youtube.com/embed/' . $video['videoId'] . '" allow="autoplay; encrypted-media" allowfullscreen></iframe>
												</div>
										</div>';
		$htmlCardBlock = '<div class="card-block">
												<h4 class="card-title">' . $video['name']. '</h4>
											</div>
									</div>';
		$embeds[] = $htmlCardImg . $htmlCardBlock;
	}
	return $embeds;
}
function BuildHTML($embeds) {
	$pregrid = '<div class="grid" id="grid" data-masonry=\'{ "itemSelector": ".grid-item"}\'>';
	$postgrid = '</div>';
	$predata = '<div class="grid-item">';
	$postdata = '</div>';
	$grid_script = '<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>';
	$final_html = '';
	$feed_ar_to_str = '';

	$final_html .= $pregrid;	
	foreach($embeds as $html) {
		$feed_ar_to_str .= $predata . $html . $postdata;
	}
	$final_html .=  $feed_ar_to_str . $postgrid . $grid_script;
	return $final_html;
}


if (hasYoutube()) {
	$access_token = LoadYoutubeCredentials();
	$youtube = GetYoutubeService($access_token);
	$channelIds = GetChannelIds($youtube);
	$channelsPlaylist = GetChannelsPlaylist($youtube, $channelIds);
	$videos = GetPlaylistUploads($youtube, $channelsPlaylist);
	$embeds = BuildEmbeds($videos);
	$html = BuildHTML($embeds);
	echo $html;
}
?>
