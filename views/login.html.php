<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>ScrumIO</title>
    <link href='https://fonts.googleapis.com/css?family=PT+Sans:regular,italic,bold' rel='stylesheet' type='text/css'>       
    <link rel="stylesheet" href="public/base.css" type="text/css" media="all" charset="utf-8">
    <link rel="stylesheet" href="public/dashboard.css" type="text/css" media="all" charset="utf-8">
    <link rel="stylesheet" href="public/board.css" type="text/css" media="all" charset="utf-8">
  </head>
  <body>
    <div>
      <header>
        <h1>Scrumio</h1>
      </header>
      <div id="login-area">
        <?php if (isset($error_description)) : ?>
          <p class="error"><?php print $error_description; ?></p>
        <?php else : ?>
          <p>To use ScrumIO you must grant ScrumIO access to your Podio. Login below.</p>
        <?php endif; ?>
        <a href="<?php print $oauth_url;?>">Login to Podio</a>
      </div>
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
