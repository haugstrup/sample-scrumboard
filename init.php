<?php
require_once('config.php');
require_once(CLIENT);
session_start();

// Setup API client and get access token
$api = Podio::instance(CLIENT_ID, CLIENT_SECRET);

// $api->debug = true;

// If there's an access token in the session, make podio-php use it
if (!empty($_SESSION['access_token'])) {
  $api->oauth->access_token = $_SESSION['access_token'];
  $api->oauth->refresh_token = $_SESSION['refresh_token'];
}
