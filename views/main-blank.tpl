<? namespace Fierce ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <base href="<?= base_url ?>" id="base_href">
  
  <title><?= $pageTitle ?></title>
  
  <style type="text/css">
    /* Fierce/css/plain.css */
    <?= file_get_contents(fierce_path . 'css/plain.css') ?>
  </style>
  
  <? foreach ($cssUrls as $cssUrl): ?>
    <style type="text/css">
      /* <?= $cssUrl ?> */
      <?= file_get_contents(base_path . $cssUrl) ?>
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
