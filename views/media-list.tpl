<? namespace F ?>

<? $pageTitle = 'Media' ?>

<? View::addCss('fierce/css/admin-media.css') ?>

<h1>Manage Images</h1>

<div class="buttons">
  <a href="javascript:void(0)" onclick="alert('not yet implemented')">Add Image</a>
</div>

<div class="media_list_wrapper">
  <table class="grid" id="media_image_list">
    <thead>
      <tr>
        <th>&nbsp;</span>
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
