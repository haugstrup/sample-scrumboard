<?php
require_once('config.php');
require_once(CLIENT);
session_start();

// Setup API client and get access token
$oauth = PodioOAuth::instance();
$baseAPI = PodioBaseAPI::instance(CLIENT_ID, CLIENT_SECRET);

// Obtain access token and init API class
if (!isset($_SESSION['access_token'])) {
  $oauth->getAccessToken('password', array('username' => USERNAME, 'password' => PASSWORD));

  $api = new PodioAPI();
  $_SESSION['access_token'] = $oauth->access_token;
  $_SESSION['refresh_token'] = $oauth->refresh_token;
  
  // Figure out which space we're on so we can build links to items
  $_SESSION['story_app'] = $api->app->get(STORY_APP_ID);
  $_SESSION['space'] = $api->space->get($_SESSION['story_app']['space_id']);
}
else {
  $oauth->access_token = $_SESSION['access_token'];
  $oauth->refresh_token = $_SESSION['refresh_token'];

  $api = new PodioAPI();
}

