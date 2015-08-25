<? namespace Fierce ?>

<? $pageTitle = 'Media' ?>

<? View::addCss('fierce/css/admin-media.css') ?>
<? View::addScript('fierce/scripts/admin-media.js') ?>

<div class="buttons media-buttons">
  <a href="javascript:void(0)" id="add_image_button">Add Image</a>
  <input type="file" id="add_image_button_upload" accept="image/*">
</div>

<h1><?= htmlspecialchars($displayName) ?></h1>

<div class="media_list_wrapper">
  <table class="grid" id="media_image_list">
    <tbody>
      <tr>
        <td>
          <? foreach ($imageItems as $item): ?>
            <div class="image_cell" data-url="<?= ltrim($item->url, '/') ?>">
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
