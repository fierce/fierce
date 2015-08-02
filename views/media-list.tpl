<? $pageTitle = 'Media' ?>

<? View::addCss('fierce/css/admin-media.css') ?>

<h1>Media</h1>

<div class="media_list_wrapper">
  <table class="grid" id="media_image_list">
    <thead>
      <tr>
        <th class="name">Images</span>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <? foreach ($imageItems as $item): ?>
            <div class="image_cell">
              <a href="<?= ltrim($item->url, '/') ?>">
                <div class="image_wrapper">
                  <?= View::thumbnail($item->url, 70, 70, false) ?>
                </div>
              </a>
              <span class="name"><?= htmlspecialchars($item->name) ?></span>
              
              <!--
              <div class="buttons">
                <a href="javascript:void(0)" confirmedHref="<?= $controller->url('delete', ['id' => $item->id]) ?>" onclick="
                  if (confirm('Are you sure you want to delete <?= addslashes(htmlspecialchars($item->name)) ?>?')) {
                    this.setAttribute('href', this.getAttribute('confirmedHref'))
                  }
                ">Delete </a>
              </div>
              -->
            </div>
          <? endforeach; ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<div class="buttons">
  <a href="javascript:void(0)" onclick="alert('not yet implemented')">Add Image</a>
</div>

<hr>

<div class="media_list_wrapper">
  <table class="grid" id="media_css_list">
    <thead>
      <tr>
        <th class="name">CSS</span>
        <th class="buttons">&nbsp;</span>
      </tr>
    </thead>
    <tbody>
      <? foreach ($cssItems as $item): ?>
        <tr>
          <td class="name"><?= htmlspecialchars($item->name) ?>
          
          <td class="buttons">
            <? if (@$item->url): ?>
              <a href="<?= ltrim($item->url, '/') ?>">View</a>
            <? endif; ?>
            <!--
            <a href="javascript:void(0)" confirmedHref="<?= $controller->url('delete', ['id' => $item->id]) ?>" onclick="
              if (confirm('Are you sure you want to delete <?= addslashes(htmlspecialchars($item->name)) ?>?')) {
                this.setAttribute('href', this.getAttribute('confirmedHref'))
              }
            ">Delete </a>
            -->
          </td>
        </tr>
      <? endforeach; ?>
    </tbody>
  </table>
</div>

<div class="buttons">
  <a href="javascript:void(0)" onclick="alert('not yet implemented')">Add CSS</a>
</div>

<hr>

<div class="media_list_wrapper">
  <table class="grid" id="media_js_list">
    <thead>
      <tr>
        <th class="name">JavaScript</span>
        <th class="buttons">&nbsp;</span>
      </tr>
    </thead>
    <tbody>
      <? foreach ($jsItems as $item): ?>
        <tr>
          <td class="name"><?= htmlspecialchars($item->name) ?>
          
          <td class="buttons">
            <? if (@$item->url): ?>
              <a href="<?= ltrim($item->url, '/') ?>">View</a>
            <? endif; ?>
            <!--
            <a href="javascript:void(0)" confirmedHref="<?= $controller->url('delete', ['id' => $item->id]) ?>" onclick="
              if (confirm('Are you sure you want to delete <?= addslashes(htmlspecialchars($item->name)) ?>?')) {
                this.setAttribute('href', this.getAttribute('confirmedHref'))
              }
            ">Delete </a>
            -->
          </td>
        </tr>
      <? endforeach; ?>
    </tbody>
  </table>
</div>

<div class="buttons">
  <a href="javascript:void(0)" onclick="alert('not yet implemented')">Add JavaScript</a>
</div>
