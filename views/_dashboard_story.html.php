<li data-id="<?= $story->item_id ?>">
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
        $status_text = $story->get_status_text();
        if ($status_text) {
          $links[] = '<span class="'.$status_text['short'].'">'.$status_text['long'].'</span>';
        }
        $links[] = '<a href="'.$story->link.'">view in podio</a>';
        if ($story->product_owner) {
          $links[] = $story->product_owner['name'];
        }
        $links[] = $story->get_estimate() .' hrs estimated';
      ?>
      <?= implode(' | ', $links); ?>
    </div>
  </div>
</li>
