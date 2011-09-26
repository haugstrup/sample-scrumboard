<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>ScrumIO | draggable, droppable, trackable scrum!</title>
    <!-- <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"> -->
    <link href='https://fonts.googleapis.com/css?family=PT+Sans:regular,italic,bold' rel='stylesheet' type='text/css'>       
    <link rel="stylesheet" href="public/base.css" type="text/css" media="all" charset="utf-8">
    <link rel="stylesheet" href="public/dashboard.css" type="text/css" media="all" charset="utf-8">
    <link rel="stylesheet" href="public/scrumboard.css" type="text/css" media="all" charset="utf-8">
    <link rel="stylesheet" href="public/tipsy/stylesheets/tipsy.css" type="text/css" media="all" charset="utf-8">
    <link rel="shortcut icon" href="public/i/favicon.png"> 
  </head>
  <body>
    <div id="navbar">
      <a id="logout" href="<?= url_for('logout');?>">Log out</a>
      <a href="#" id="switch-view">Switch view</a> | 
      Sprints: 
      <ul class="sprints">
        <?php foreach ($sprints as $item) : ?>
          <li class="<?= $sprint->item_id == $item['item_id'] ? 'selected' : '' ?>"><a href="<?= url_for('/show/'.$item['item_id']);?>"><?= $item['title']; ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div id="main">
      <div id="dashboard">

        <div id="sidebar">
          <div class="sprint_status">
            <div class="on_target_text <?= $sprint->get_on_target_delta() >= 0 ? 'over' : 'under' ?>"><?= $sprint->get_on_target_delta() >= 0 ? '+'.$sprint->get_on_target_delta() : $sprint->get_on_target_delta() ?> hrs</div>
            <div class="total_hours"><?= $sprint->get_time_left();?> hours left, <?= $sprint->get_estimate();?> total hours<br><?= $sprint->get_working_days_left(); ?> days left, <?= $sprint->get_planned_daily_burn(); ?> hrs/day burn</div>
          </div>
          <div class="graph total_graph">
            <div class="box-wrap">
              <div class="target" title="Target: <?= $sprint->get_on_target_value(); ?> hours" style="left: <?= $sprint->get_current_target_percent(); ?>%;"></div>
              <div class="actual" title="Finished: <?= $sprint->get_finished(); ?> hours" style="width: <?= $sprint->get_current_percent(); ?>%;"></div>
            </div>
          </div>
        </div>

        <ul class="stories">
          <?php foreach ($sprint->stories as $story) : ?>
            <?= render('_dashboard_story.html.php', NULL, array('story' => $story)); ?>
          <?php endforeach; ?>
        </ul>


      </div>
      <div id="stories" class="story-view hidden" data-count="<?= count($sprint->states); ?>">
        <div class="header">
          <?php foreach ($sprint->states as $state) : ?>
            <?= '<h1>'.$state.'</h1>'; ?>
          <?php endforeach; ?>
        </div>
        <div class="items">
          <?php foreach ($sprint->stories as $story) : ?>
            <?= render('_story.html.php', NULL, array('story' => $story)); ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div id="overlay"></div>
    <script type="text/javascript" charset="utf-8">
      var update_url_base = "<?= url_for('/item'); ?>";
    </script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script> 
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.js"></script>
    <script src="public/tipsy/javascripts/jquery.tipsy.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/lib/jquery.ui.touch.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/lib/Function.prototype.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/lib/Podio.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/lib/Podio.Event.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/lib/Podio.Event.UI.js" type="text/javascript" charset="utf-8"></script>
    <script src="public/scrumboard.js" type="text/javascript" charset="utf-8"></script>
  </body>
</html>
