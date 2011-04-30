<div id="story-view-<?= $story->item_id ?>" class="story-view hidden">
  <div class="header">
    <?php foreach ($story->states as $state) : ?>
      <?= '<h1><span>'.$state.'</span></h1>'; ?>
    <?php endforeach; ?>
  </div>

  <div class="items">
    <div class="story-group" id="story-<?= $story->item_id ?>">
      <h2><?= $story->title ?></h2>
      <?php if ($story->product_owner) : ?>
        <div class="owner">
          <img src="https://download.podio.com/<?= $story->product_owner['avatar']; ?>/tiny" width="16" height="16">
          <?= $story->product_owner['name']; ?>
        </div>
      <?php endif; ?>
      <?php $i = 0;foreach ($story->get_items_by_state() as $state => $collection) : ?>
        <div class="state state-<?= $i; ?> <?= str_replace(' ', '-', strtolower($state)); ?>">
          <ul class="story-item-state" data-state="<?= $state; ?>">
            <?php foreach ($collection as $item) : ?>
              <?= render('_item.html.php', NULL, array('item' => $item)); ?>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php $i++; ?>
      <?php endforeach; ?>
    </div>
  </div>
</div>
