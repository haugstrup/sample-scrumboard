<div class="story-group" id="story-<?= $story->item_id ?>">
  <h2><?= $story->title ?></h2>
  <?php if ($story->product_owner) : ?>
    <div class="owner">
      <b>Product owner:</b> 
      <img src="https://download.podio.com/<?= $story->product_owner['avatar']; ?>/tiny" width="16" height="16">
      <?= $story->product_owner['name']; ?>
      <?php $responsible = $story->get_responsible(); ?>
      <?php if ($responsible): ?>
        <span class="responsible">
          <b>Responsible:</b> 
          <?php foreach($responsible as $user): ?>
            <img src="https://download.podio.com/<?= $user['avatar']; ?>/tiny" width="16" height="16">
            <?= $user['name']; ?>
          <?php endforeach; ?>
        </span>
      <?php endif; ?>
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
