<? namespace Fierce ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <base href="<?= BASE_URL ?>" id="base_href">
  
  <title><?= $pageTitle ?></title>
  
  <style type="text/css">
    /* Fierce/css/plain.css */
    <?= file_get_contents(FIERCE_PATH . 'css/plain.css') ?>
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

<?= $contentViewHtml ?>

</body>
</html>
