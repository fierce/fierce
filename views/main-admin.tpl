<? namespace Fierce ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <base href="<?= BASE_URL ?>" id="base_href">
  
  <title><?= $pageTitle ?></title>
  
  <style type="text/css">
    /* fierce/css/admin-base.css */
    <?= file_get_contents(BASE_PATH . 'fierce/css/admin-base.css') ?>
  </style>
  
  <style type="text/css">
    /* css/admin.css */
    <?= file_get_contents(BASE_PATH . 'css/admin.css') ?>
  </style>
  
  <? foreach ($cssUrls as $cssUrl): ?>
    <style type="text/css">
      /* <?= $cssUrl ?> */
      <?= file_get_contents(BASE_PATH . $cssUrl) ?>
    </style>
  <? endforeach ?>
  
  <? foreach ($scriptUrls as $scriptUrl): ?>
    <script type="text/javascript" src="<?= htmlspecialchars($scriptUrl) ?>" defer="defer"></script>
  <? endforeach ?>
</head>
<body>

<? if ($user = Auth::loggedInUser()): ?>
<div id="header" class="buttons">
  <div id="login-status">
    <span class="username"><?= htmlspecialchars($user->email) ?></span>
    <a href="login?do=logout">Log Out</a>
  </div>
  
  <div id="nav">
    <? View::includeView('admin-nav.tpl') ?>
  </div>
</div>
<? endif ?>

<?= $contentViewHtml ?>

</body>
</html>
