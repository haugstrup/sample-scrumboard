<?php
require_once('config.php');
require_once(CLIENT);
session_start();

// Setup API client and get access token
$oauth = PodioOAuth::instance();
$baseAPI = PodioBaseAPI::instance(API_SERVER, CLIENT_ID, CLIENT_SECRET);

// Obtain access token and init API class
if (!isset($_SESSION['access_token'])) {
  $oauth->getAccessToken('password', array('username' => USERNAME, 'password' => PASSWORD));
  $_SESSION['access_token'] = $oauth->access_token;
  $_SESSION['refresh_token'] = $oauth->refresh_token;
}
else {
  $oauth->access_token = $_SESSION['access_token'];
  $oauth->refresh_token = $_SESSION['refresh_token'];
}
$api = new PodioAPI();

