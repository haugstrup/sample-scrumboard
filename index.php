<?php
require_once('init.php');

// Grab an active sprint.
$filters = array(array('key' => SPRINT_STATE_ID, 'values' => array('Active')));
$sprints = $api->item->getItems(SPRINT_APP_ID, 1, 0, 'title', 0, $filters);
$sprint_id = $sprints['items'][0]['item_id'];

// Get all stories in this sprint
$filters = array(array('key' => STORY_SPRINT_ID, 'values' => array(STORY_SPRINT_FILTER_VALUE)));
$stories = $api->item->getItems(STORY_APP_ID, 200, 0, 'title', 0, $filters);

// Get all story items for each story
$items = array();
foreach ($stories['items'] as $story) {
  $items[$story['item_id']] = array(
    'Not started' => array(),
    'Dev started' => array(),
    'Dev done' => array(),
    'PO done' => array(),
  );
  $filters = array(array('key' => ITEM_STORY_ID, 'values' => array($story['item_id'])));
  $raw = $api->item->getItems(ITEM_APP_ID, 200, 0, 'title', 0, $filters);
  
  // Locate the value of the item state
  $state = 'Not started';
  foreach ($raw['items'] as $item) {
    foreach ($item['fields'] as $field) {
      if ($field['external_id'] == 'state') {
        $state = $field['values'][0]['value'];
        break;
      }
    }
    $items[$story['item_id']][$state][] = $item;
  }
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>Podio Scrum Board</title>
    <link rel="stylesheet" href="scrumboard.css" type="text/css" media="all" charset="utf-8">
  </head>
  <body>
    <div id="#main">
      <div class="header">
        <h1><span>Not started</span></h1>
        <h1><span>Dev. started</span></h1>
        <h1><span>Dev. done</span></h1>
        <h1><span>PO done</span></h1>
      </div>
      <?php
      foreach ($stories['items'] as $story) {
        $owner = '';
        foreach ($story['fields'] as $field) {
          if ($field['external_id'] == 'product-owner') {
            $owner = '<div class="owner"><img src="https://download.podio.com/'.$field['values'][0]['value']['avatar'].'/tiny" width="16" height="16"> '.$field['values'][0]['value']['name'].'</div>';
            break;
          }
        }

        print '<div class="story-group" id="story-'.$story['item_id'].'">';
        print '<h2>'.$story['title'].'</h2>';
        print $owner;
        
        foreach ($items[$story['item_id']] as $state => $collection) {
          print '<div class="state '.str_replace(' ', '-', strtolower($state)).'">';
          print '<ul class="story-item-state" data-state="'.$state.'">';
          foreach ($collection as $item) {
            print '<li class="story-item" data-id="'.$item['item_id'].'">';
            foreach ($item['fields'] as $field) {
              if ($field['external_id'] == 'responsible') {
                if ($field['values'][0]['value']['avatar']) {
                  print '<div class="responsible"><img src="https://download.podio.com/'.$field['values'][0]['value']['avatar'].'/tiny" width="16" height="16"></div>';
                }
                break;
              }
            }
            print '<h3>'.$item['title'].'</h3>';
            foreach ($item['fields'] as $field) {
              if ($field['external_id'] == 'notes') {
                if ($field['values'][0]['value']) {
                  print '<div class="notes">'.$field['values'][0]['value'].'</div>';
                }
                break;
              }
            }
            print '</li>';
          }
          print '</ul>';
          print '</div>';
        }
        
        print '</div>';
      }
      
      ?>
    </div>
    <div id="footer">
      This is a demo of the Podio API. See more on <a href="https://github.com/podio/sample-scrumboard">Github</a>
    </div>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script> 
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.js"></script>
    <script src="scrumboard.js" type="text/javascript" charset="utf-8"></script>
    </script>
  </body>
</html>
