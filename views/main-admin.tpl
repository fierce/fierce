<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <base href="<?= BASE_URL ?>">
  
  <title><?= $pageTitle ?></title>
  
  <style type="text/css">
    <? require BASE_PATH . 'fierce/css/admin-base.css' ?>
    <? require BASE_PATH . 'css/admin.css' ?>
  </style>
  
  <? foreach ($scripts as $script): ?>
    <script type="text/javascript" src="<?= htmlspecialchars($script) ?>" defer="defer"></script>
  <? endforeach ?>
</head>
<body>

<? if ($user = Auth::loggedInUser()): ?>
<div id="header" class="buttons">
  <div id="login-status">
    <?= htmlspecialchars($user->email) ?>
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
