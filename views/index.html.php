<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>ScrumIO</title>
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/themes/base/jquery-ui.css" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=PT+Sans:regular,italic,bold' rel='stylesheet' type='text/css'>       
    <link rel="stylesheet" href="public/scrumboard.css" type="text/css" media="all" charset="utf-8">
  </head>
  <body>
    <div id="main">
      <header>
        <h1>Scrumio</h1>
      </header>
      <div id="dashboard">
        <div class="graph total_graph">
          <div class="box-wrap">
            <div class="target" style="left: <?= $sprint->get_current_target_percent(); ?>%;"></div>
            <div class="actual" style="width: <?= $sprint->get_current_percent(); ?>%;"></div>
          </div>
        </div>

        <ul class="stories">
          <?php foreach ($sprint->stories as $story) : ?>
            <?= render('_dashboard_story.html.php', NULL, array('story' => $story)); ?>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php foreach ($sprint->stories as $story) : ?>
        <?= render('_story.html.php', NULL, array('story' => $story)); ?>
      <?php endforeach; ?>
      
    </div>
    <script type="text/javascript" charset="utf-8">
      var update_url_base = "<?= url_for('/item'); ?>";
    </script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script> 
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.js"></script>
    <script src="public/lib/jquery.ui.touch.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/lib/Function.prototype.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/lib/Podio.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/lib/Podio.Event.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/lib/Podio.Event.UI.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/scrumboard.js" type="text/javascript" charset="utf-8"></script>
  </body>
</html>
