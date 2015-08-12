<? $pageTitle = $entity . ' List' ?>

<h1><?= $entity ?> List</h1>

<div class="buttons">
  <a href="<?= $controller->url('add') ?>">Add <?= $entity ?></a>
</div>

<table class="grid" id="<?= strtolower($entity) ?>_list">
  <thead>
    <tr>
      <? foreach ($listFields as $field => $name): ?>
        <th class="<?= $field ?>"><?= htmlspecialchars($name) ?></span>
      <? endforeach; ?>
      <th class="buttons">&nbsp;</span>
    </tr>
  </thead>
  <tbody>
    <? foreach ($items as $item): ?>
      <tr>
        <? foreach ($listFields as $field => $name): ?>
          <td class="<?= $field ?>"><?= preg_match('/_html$/', $field) ? $item->$field : htmlspecialchars($item->$field) ?></td>
        <? endforeach; ?>
        
        <td class="buttons">
          <? if (@$item->url): ?>
            <a href="<?= ltrim($item->url, '/') ?>">View</a>
          <? endif; ?>
          <a href="<?= $controller->url('edit', ['id' => $item->id]) ?>">Edit</a>
          <a href="javascript:void(0)" confirmedHref="<?= $controller->url('delete', ['id' => $item->id]) ?>" onclick="
            if (confirm('Are you sure you want to delete <?= addslashes(htmlspecialchars(@$item->$displayField)) ?>?')) {
              this.setAttribute('href', this.getAttribute('confirmedHref'))
            }
          ">Delete </a>
        </td>
      </tr>
    <? endforeach; ?>
  </tbody>
</table>
