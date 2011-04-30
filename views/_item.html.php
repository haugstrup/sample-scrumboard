<li class="story-item" data-id="<?= $item->item_id ?>" data-url="<?= $_SESSION['space']['url']; ?>item/<?= $item->item_id ?>">
  <h3>
    <div class="draghandle"></div>
    <?= $item->title ?>
    <div class="toggle"></div>
  </h3>
  <div class="story-item-details">

    <div class="duration">
      <?= $item->time_left ?>/<?= $item->estimate ?>
    </div>

    <?php if ($item->responsible) : ?>
      <div class="responsible">
        <div class="avatar">
          <img src="https://download.podio.com/<?= $item->responsible['avatar']; ?>/medium" width="100" height="100">
        </div>
        <div class="name"><?= $item->responsible['name']; ?></div>
        <br style="clear:both;">
      </div>
    <?php endif; ?>

    <br style="clear:both;">
  </div>
</li>
