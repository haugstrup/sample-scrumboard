<li>
    <a href="#" data-id="<?= $story->item_id ?>"><?= $story->title ?></a>
    <ul class="status">
    <?php
      foreach ($story->get_items_by_state() as $state => $list) {
        print '<li class="'. str_replace(' ', '-', strtolower($state)) .'"><span>'.count($list).'</span>'. $state .'</li>';
      }
    ?>
    </ul>

    <div class="numbers">
    <?= $story->get_time_left(); ?> l / <?= $story->get_estimate(); ?> e / <?= count($story->items); ?> tasks
    </div>
  </li>
