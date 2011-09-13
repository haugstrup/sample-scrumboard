<?php

class ScrumioItem {
  
  public $item_id;
  public $title;
  public $estimate;
  public $time_left;
  public $responsible;
  public $state;
  public $story_id;
  
  public function __construct($item) {
    global $api;
    // Set Item properties
    $this->item_id = $item['item_id'];
    $this->title = $item['title'];
    
    foreach ($item['fields'] as $field) {
      if ($field['field_id'] == ITEM_STORY_ID) {
        $this->story_id = $field['values'][0]['value']['item_id'];
      }
      if ($field['field_id'] == ITEM_STATE_ID) {
        $this->state = $field['values'][0]['value'];
      }
      if ($field['field_id'] == ITEM_ESTIMATE_ID) {
        $this->estimate = 0;
        if ($field['values'][0]['value'] > 0) {
          $this->estimate = $field['values'][0]['value']/3600;
        }
      }
      if ($field['field_id'] == ITEM_TIMELEFT_ID) {
        $this->time_left = 0;
        if ($field['values'][0]['value'] > 0) {
          $this->time_left = $field['values'][0]['value']/3600;
        }
      }
      if ($field['field_id'] == ITEM_RESPONSIBLE_ID) {
        $this->responsible = array();
        if ($field['values'][0]['value'] > 0) {
          if ($field['values'][0]['value']['avatar']) {
            $this->responsible = $field['values'][0]['value'];
          }
        }
      }
    }
  }
  
}

class ScrumioStory {

  public $item_id;
  public $title;
  public $product_owner;
  public $states;
  public $total_days;
  public $remaining_days;
  public $items;
  
  public function __construct($item, $items, $estimate, $time_left, $states, $total_days, $remaining_days) {
    global $api;
    // Set Story properties
    $this->item_id = $item['item_id'];
    $this->title = $item['title'];
    $this->link = $item['link'];
    foreach ($item['fields'] as $field) {
      if ($field['field_id'] == STORY_OWNER) {
        $this->product_owner = $field['values'][0]['value'];
        break;
      }
    }
    
    // Get all items for this story
    $this->items = $items;
    $this->estimate = $estimate;
    $this->time_left = $time_left;
    
    $this->states = $states;
    $this->total_days = $total_days;
    $this->remaining_days = $remaining_days;
  }
  
  public function get_responsible() {
    $list = array();
    foreach ($this->items as $item) {
      if ($item->responsible) {
        $list[$item->responsible['user_id']] = $item->responsible;
      }
    }
    return $list;
  }
  
  public function get_items_by_state() {
    $list = array();
    foreach ($this->states as $state) {
      $list[$state] = array();
    }
    
    foreach ($this->items as $item) {
      $state = $item->state ? $item->state : STATE_NOT_STARTED;
      $list[$state][] = $item;
    }
    
    return $list;
  }
  
  public function get_status_text() {
    $states = $this->get_items_by_state();
    $total = count($this->items);
    $return = array();
    
    if (count($states['Dev done']) > 0 && $total == (count($states['Dev done'])+count($states['QA done'])+count($states['PO done']))) {
      $return = array('short' => 'testing', 'long' => 'ready for testing!');
    }
    elseif (count($states['QA done']) > 0 && $total == (count($states['QA done'])+count($states['PO done']))) {
      $return = array('short' => 'po', 'long' => 'ready for PO signoff!');
    }
    elseif (count($states['PO done']) > 0 && $total == count($states['PO done'])) {
      $return = array('short' => 'done', 'long' => 'all finished!');
    }
    
    return $return;
  }
  
  public function get_time_left() {
    return $this->time_left;
  }
  
  public function get_estimate() {
    return $this->estimate;
  }
  
  public function get_on_target_value() {
    $estimate = $this->get_estimate();
    $hours_per_day = $estimate/$this->total_days;
    $target_value = round($estimate-($this->remaining_days*$hours_per_day));
    return $target_value > $estimate ? $estimate : $target_value;
  }
  
  public function get_current_percent() {
    $target = $this->get_on_target_value();
    $total = $this->get_estimate();
    $current = $total-$this->get_time_left();
    $target_percent = $target/$total*100;
    return $current/$total*100;
  }
  
  public function get_current_target_percent() {
    $target = $this->get_on_target_value();
    $total = $this->get_estimate();
    $current = $total-$this->get_time_left();
    return $target/$total*100;
  }
  
}

class ScrumioSprint {
  
  public $item_id;
  public $title;
  public $start_date;
  public $end_date;
  public $states;
  public $total_days;
  public $remaining_days;
  public $stories;
  
  public function __construct($sprint) {
    global $api;
    // Locate available states
    $items_app = $api->app->get(ITEM_APP_ID);
    $this->states = array();
    if(is_array($items_app['fields'])) {
      foreach ($items_app['fields'] as $field) {
        if ($field['field_id'] == ITEM_STATE_ID) {
          $this->states = $field['config']['settings']['allowed_values'];
          break;
        }
      }
    }
    // Find active sprint
    // $filters = array(array('key' => SPRINT_STATE_ID, 'values' => array('Active')));
    // $sprints = $api->item->getItems(SPRINT_APP_ID, 1, 0, 'title', 0, $filters);
    // $sprint = $sprints['items'][0];
    $sprint_id = $sprint['item_id'];
    
    // Set sprint properties
    $this->item_id = $sprint['item_id'];
    $this->title = $sprint['title'];
    foreach ($sprint['fields'] as $field) {
      if ($field['type'] == 'date') {
        $this->start_date = date_create($field['values'][0]['start'], timezone_open('UTC'));
        $this->end_date = date_create($field['values'][0]['end'], timezone_open('UTC'));
      }
    }

    // Get all stories in this sprint
    $filters = array(array('key' => STORY_SPRINT_ID, 'values' => array($sprint_id)));
    $stories = $api->item->getItems(STORY_APP_ID, 200, 0, 'title', 0, $filters);
    
    // Grab all story items for all stories in one go
    $stories_ids = array();
    $stories_items = array();
    $stories_estimates = array();
    $stories_time_left = array();
    foreach ($stories['items'] as $story) {
      $stories_ids[] = $story['item_id'];
      $stories_items[$story['item_id']] = array();
      $stories_estimates[$story['item_id']] = 0;
      $stories_time_left[$story['item_id']] = 0;
    }
    $filters = array(array('key' => ITEM_STORY_ID, 'values' => $stories_ids));
    $raw = $api->item->getItems(ITEM_APP_ID, 200, 0, 'title', 0, $filters);
    foreach ($raw['items'] as $item) {
      $item = new ScrumioItem($item);
      $stories_items[$item->story_id][] = $item;
      $stories_estimates[$item->story_id] = $stories_estimates[$item->story_id] + $item->estimate;
      $stories_time_left[$item->story_id] = $stories_time_left[$item->story_id] + $item->time_left;
    }

    foreach ($stories['items'] as $story) {
      $items = $stories_items[$story['item_id']];
      $estimate = $stories_estimates[$story['item_id']] ? $stories_estimates[$story['item_id']] : '0';
      $time_left = $stories_time_left[$story['item_id']] ? $stories_time_left[$story['item_id']] : '0';
      
      if (count($items) > 0) {
        $this->stories[] = new ScrumioStory($story, $items, $estimate, $time_left, $this->states, $this->get_working_days(), $this->get_working_days_left());
      }
    }
    
  }
  
  public function get_working_days() {
    return getWorkingDays(date_format($this->start_date, 'Y-m-d'), date_format($this->end_date, 'Y-m-d'));
  }
  
  public function get_working_days_left() {
    $start_date = date_create('now', timezone_open('UTC'));
    
    // We substract 1 here to be able to 'chase the target' rather than 'working ahead'
    return getWorkingDays(date_format($start_date, 'Y-m-d'), date_format($this->end_date, 'Y-m-d'))-1;
  }
  
  public function get_time_left() {
    static $list;
    if (!isset($list[$this->item_id])) {
      $list[$this->item_id] = 0;
      foreach ($this->stories as $story) {
        $list[$this->item_id] = $list[$this->item_id]+$story->get_time_left();
      }
    }
    return $list[$this->item_id] ? $list[$this->item_id] : '0';
  }
  
  public function get_estimate() {
    static $list;
    if (!isset($list[$this->item_id])) {
      $list[$this->item_id] = 0;
      foreach ($this->stories as $story) {
        $list[$this->item_id] = $list[$this->item_id]+$story->get_estimate();
      }
    }
    return $list[$this->item_id] ? $list[$this->item_id] : '0';
  }
  
  public function get_on_target_value() {
    static $list;
    if (!isset($list[$this->item_id])) {
      $estimate = $this->get_estimate();
      $total_days = $this->get_working_days();
      $remaining_days = $this->get_working_days_left();
      $hours_per_day = $estimate/$total_days;
      $target_value = round($estimate-($remaining_days*$hours_per_day));
      $list[$this->item_id] = $target_value > $estimate ? $estimate : $target_value;
      
    }
    return $list[$this->item_id];
  }
  
  public function get_planned_daily_burn() {
    static $list;
    if (!isset($list[$this->item_id])) {
      $estimate = $this->get_estimate();
      $total_days = $this->get_working_days();
      $hours_per_day = $estimate/$total_days;
      $list[$this->item_id] = round($hours_per_day, 2);
    }
    return $list[$this->item_id];
  }

  public function get_current_percent() {
    $target = $this->get_on_target_value();
    $total = $this->get_estimate();
    $current = $total-$this->get_time_left();
    $target_percent = $target/$total*100;
    return $current/$total*100;
  }
  
  public function get_current_target_percent() {
    $target = $this->get_on_target_value();
    $total = $this->get_estimate();
    $current = $total-$this->get_time_left();
    return $target/$total*100;
  }

  public function get_finished() {
    return $this->get_estimate()-$this->get_time_left();
  }

  public function get_on_target_delta() {
    return $this->get_finished()-$this->get_on_target_value();
  }
  
}

//The function returns the no. of business days between two dates and it skips the holidays
function getWorkingDays($startDate,$endDate,$holidays = array()){
  //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
  //We add one to inlude both dates in the interval.
  $days = (strtotime($endDate) - strtotime($startDate)) / 86400 + 1;

  $no_full_weeks = floor($days / 7);
  $no_remaining_days = fmod($days, 7);

  //It will return 1 if it's Monday,.. ,7 for Sunday
  $the_first_day_of_week = gmdate("N",strtotime($startDate));
  $the_last_day_of_week = gmdate("N",strtotime($endDate));

  //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
  //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
  if ($the_first_day_of_week <= $the_last_day_of_week){
    if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
    if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
  }
  else{
    if ($the_first_day_of_week <= 6) {
      //In the case when the interval falls in two weeks, there will be a weekend for sure
      $no_remaining_days = $no_remaining_days - 2;
    }
  }

  //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
 $workingDays = $no_full_weeks * 5;
  if ($no_remaining_days > 0 )
  {
    $workingDays += $no_remaining_days;
  }

  //We subtract the holidays
  foreach($holidays as $holiday){
    $time_stamp=strtotime($holiday);
    //If the holiday doesn't fall in weekend
    if (strtotime($startDate) <= $time_stamp && $time_stamp <= strtotime($endDate) && gmdate("N",$time_stamp) != 6 && gmdate("N",$time_stamp) != 7)
      $workingDays--;
  }

  return $workingDays;
}
