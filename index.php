<?php
require_once 'vendor/limonade.php';
require_once 'scrumio.classes.php';
require_once 'init.php';
require_once 'helpers.php';

function configure() {
  option('OAUTH_REDIRECT_URI', 'http://'.$_SERVER['HTTP_HOST'].url_for('authorize'));
  option('OAUTH_URL', htmlentities(OAUTH_ENDPOINT.'?response_type=code&client_id='.CLIENT_ID.'&redirect_uri='.rawurlencode(option('OAUTH_REDIRECT_URI'))));
}

dispatch('/', 'scrumboard');
dispatch('/show/:id', 'scrumboard');
  function scrumboard() {
    global $api;
    // If we have an access token, show the scrumboard
    if (isset($_SESSION['access_token']) && $_SESSION['access_token'] && isset($_SESSION['story_app']) && $_SESSION['story_app']) {

      // Grab sprints and find current sprint
      // $filters = array(array('key' => SPRINT_STATE_ID, 'values' => array('Active')));
      try {
        $sprints = PodioItem::filter( SPRINT_APP_ID, array(
          'limit' => 5,
          'sort_by' => 'created_on',
          'sort_desc' => 1
        ));
      }
      catch (PodioError $e) {
        die("There was an error. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']}</b>. The URL was <b>{$e->url}</b><br><a href='".url_for('logout')."'>Log out</a>");
      }

      foreach ($sprints['items'] as $item) {
        if (params('id') == $item['item_id']) {
          $current_sprint = $item;
          break;
        }
        else {
          foreach ($item['fields'] as $field) {
            if ($field['field_id'] == SPRINT_STATE_ID) {
              if ($field['values'][0]['value'] == 'Active') {
                $current_sprint = $item;
              }
            }
          }
        }
      }

      $sprint = new ScrumioSprint($current_sprint);
      return html('index.html.php', NULL, array('sprint' => $sprint, 'sprints' => $sprints['items']));
    }
    else {
      // No access token, show the "login" screen
      return html('login.html.php', NULL, array('oauth_url' => option('OAUTH_URL')));
    }
  }

dispatch('/authorize', 'authorize');
  function authorize() {
    global $api;

    $story_app = NULL;

    // Successful authorization. Store the access token in the session
    if (!isset($_GET['error'])) {
      try {
	Podio::authenticate('authorization_code', array('code' => $_GET['code'], 'redirect_uri' => option('OAUTH_REDIRECT_URI')));
        $_SESSION['access_token'] = Podio::$oauth->access_token;
        $_SESSION['refresh_token'] = Podio::$oauth->refresh_token;
        $story_app = PodioApp::get(STORY_APP_ID);
      }
      catch (PodioError $e) {
        die("There was an error. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']}</b><br><a href='".url_for('/')."'>Go back</a>");
      }
    }

    if ($story_app) {
      //$_SESSION['story_app'] = $story_app;      
      //$_SESSION['space'] = PodioSpace::get($_SESSION['story_app']['space_id']);
      /* 
      $_SESSION['story_app'] and $_SESSION['space'] are only used inside check
      in scrumboard() and their values are never used, so set them to something
      just to pass these checks
      */
      $_SESSION['space'] = STORY_APP_ID;
      $_SESSION['story_app'] = STORY_APP_ID;  
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
    PodioItemField::update($item_id, ITEM_STATE_ID, $data);

    // Set time_left to '0' when moving to one of the 'done' states
    if (in_array($state, array(STATE_DEV_DONE, STATE_QA_DONE, STATE_PO_DONE))) {
          PodioItemField::update($item_id, ITEM_TIMELEFT_ID, array(array('value' => 0)), 1);
    }
    // Reset time left when moving to Not Started
    elseif ($state == STATE_NOT_STARTED) {
      $item = PodioItem::get_basic($item_id);
      $item = new ScrumioItem($item);
      PodioItemField::update($item_id, ITEM_TIMELEFT_ID, array(array('value' => $item->estimate*60*60)), 1);
    }
    return txt('ok');
  }

dispatch('/logout', 'logout');
  function logout() {
    unset($_SESSION['access_token']);
    unset($_SESSION['refresh_token']);
    unset($_SESSION['space']);
    unset($_SESSION['story_app']);
    redirect_to('');
  }

run();
