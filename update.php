<?php

require_once('init.php');
header('Content-type: text/plain;charset=utf-8');

$item_id = (int)$_POST['item_id'];
$state = $_POST['state'];

$data = array(array('value' => $state));
$api->item->updateFieldValue($item_id, ITEM_STATE_ID, $data, 1);

print 'ok';
exit();

