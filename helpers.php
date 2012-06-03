<?php

function avatar_url($user_id, $size = 'tiny') {
  return 'https://files.podio.com/'.$user_id.'/'.$size;
}

function story_area_field($app) {
  if (defined('STORY_AREA_ID')) {
    foreach ($app['fields'] as $field) {
      if ($field['field_id'] == STORY_AREA_ID) {
        return $field;
      }
    }
  }
  return false;
}
