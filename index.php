<?php
require_once 'vendor/limonade.php';
require_once 'scrumio.classes.php';
require_once 'init.php';

dispatch('/', 'scrumboard');
  function scrumboard() {
    $sprint = new ScrumioSprint();
    return html('index.html.php', NULL, array('sprint' => $sprint));
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
