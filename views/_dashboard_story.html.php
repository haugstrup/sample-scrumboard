<li data-id="<?= $story->item_id ?>" class="<?= implode(' ', $story->get_areas_class_list()); ?>">
  <div class="body">
    <div class="status-area">
      <ul class="status">
      <?php
        foreach ($story->get_items_by_state() as $state => $list) {
          print '<li style="height: '.round(count($list)/count($story->items)*100).'%;" class="'. str_replace(' ', '-', strtolower($state)) .'" title="'.$state.': '.count($list).' tasks"></li>';
        }
      ?>
      </ul>
      <div class="status-text"><div class="number"><?= $story->get_time_left(); ?></div><div class="label">hrs left</div></div>
    </div>
    <span class="title"><?= $story->title ?></span>
    <div class="metadata">
      <?php
        $links = array();
        // $status_text = $story->get_status_text();
        // if ($status_text) {
        //   $links[] = '<span class="'.$status_text['short'].'">'.$status_text['long'].'</span>';
        // }
        if ($story->areas) {
          $areas = array();
          foreach ($story->areas as $area) {
            $areas[] = '<span class="area" style="background-color:#'.$area['color'].'">'.$area['text'].'</span>';
          }
          $links[] = implode('', $areas);
        }
        $links[] = '<span class="estimate">' . $story->get_estimate() .' hrs estimated</span>';
        $links[] = '<a href="'.$story->link.'" class="external-link" target="_blank">view in podio</a>';
      ?>
      <?= implode('', $links); ?>
    </div>
  </div>
</li>
