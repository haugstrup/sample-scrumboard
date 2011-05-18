<?php
require_once('config.php');
require_once(CLIENT);
session_start();

// Setup API client and get access token
$oauth = PodioOAuth::instance();
$baseAPI = PodioBaseAPI::instance(CLIENT_ID, CLIENT_SECRET);

// If there's an access token in the session, make podio-php use it
if (!empty($_SESSION['access_token'])) {
  $oauth->access_token = $_SESSION['access_token'];
  $oauth->refresh_token = $_SESSION['refresh_token'];
}
$api = new PodioAPI();

