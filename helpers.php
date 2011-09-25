<?php

function avatar_url($user_id, $size = 'tiny') {
  return 'https://files.podio.com/'.$user_id.'/'.$size;
}

