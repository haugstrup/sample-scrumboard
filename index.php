<?php
require_once 'vendor/limonade.php';
require_once 'scrumio.classes.php';
require_once 'init.php';

function configure() {
  option('OAUTH_REDIRECT_URI', 'http://'.$_SERVER['HTTP_HOST'].url_for('authorize'));
  option('OAUTH_URL', htmlentities(OAUTH_ENDPOINT.'?response_type=code&client_id='.CLIENT_ID.'&redirect_uri='.rawurlencode(option('OAUTH_REDIRECT_URI'))));
}

dispatch('/', 'scrumboard');
  function scrumboard() {
    // If we have an access token, show the scrumboard
    if (isset($_SESSION['access_token']) && $_SESSION['access_token'] && isset($_SESSION['story_app']) && $_SESSION['story_app']) {
      $sprint = new ScrumioSprint();
      return html('index.html.php', NULL, array('sprint' => $sprint));
    }
    else {
      // No access token, show the "login" screen
      return html('login.html.php', NULL, array('oauth_url' => option('OAUTH_URL')));
    }
  }

dispatch('/authorize', 'authorize');
  function authorize() {
    global $oauth;
    
    $story_app = NULL;
    
    // Successful authorization. Store the access token in the session
    if (!isset($_GET['error'])) {
      $oauth->getAccessToken('authorization_code', array('code' => $_GET['code'], 'redirect_uri' => option('OAUTH_REDIRECT_URI')));
      $api = new PodioAPI();
      $_SESSION['access_token'] = $oauth->access_token;
      $_SESSION['refresh_token'] = $oauth->refresh_token;
      $story_app = $api->app->get(STORY_APP_ID);
    }
    
    if ($story_app) {
      $_SESSION['story_app'] = $story_app;
      $_SESSION['space'] = $api->space->get($_SESSION['story_app']['space_id']);
      redirect_to('');
    }
    else {
      // Something went wrong. Display appropriate error message.
      unset($_SESSION['access_token']);
      unset($_SESSION['refresh_token']);
      $error_description = !empty($_GET['error_description']) ? htmlentities($_GET['error_description']) : 'You do not have access to the ScrumIO apps. Try logging in as a different user.';
      return html('login.html.php', NULL, array('oauth_url' => option('OAUTH_URL'), 'error_description' => $error_description));
    }
  }

dispatch_put('/item/:item_id', 'update_time_left'); 
  function update_time_left() {
    global $api;
    $item_id = params('item_id');
    $state = $_POST['state'];
    
    $data = array(array('value' => $state));
    $api->item->updateFieldValue($item_id, ITEM_STATE_ID, $data, 1);
    if ($state == STATE_DEV_DONE) {
      $api->item->updateFieldValue($item_id, ITEM_TIMELEFT_ID, array(array('value' => 0)), 1);
    }
    return txt('ok');
  }
run();
