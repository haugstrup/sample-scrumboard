<ul>
  <?php foreach($story_area_field['config']['settings']['options'] as $option): ?>
    <li data-id="<?= $option['id']?>" data-color="#<?= $option['color']?>"><?= $option['text']?></li>
  <?php endforeach ?>
</ul>
