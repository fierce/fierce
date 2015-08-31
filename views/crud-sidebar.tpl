<? namespace Fierce ?>

<? $pageTitle = $nounPlural ?>



<div class="sidebar_wrapper">
  <div class="sidebar">
    <h1>Manage <?= $nounPlural ?></h1>
    <? foreach ($categories as $categoryValue => $categoryName): ?>
      <table class="grid" id="<?= strtolower($entity) ?>_list">
        <thead>
          <tr>
            <th colspan="<?= count($listFields) + 1 ?>"><?= htmlspecialchars($categoryName) ?></th>
          </tr>
        </thead>
        <tbody>
          <? $foundItems = false ?>
            <? foreach ($items as $sidebarItem): ?>
              <? if ($sidebarItem->$categoryField != $categoryValue) continue ?>
              
              <? $foundItems = true ?>
              
              <tr<? if ($sidebarItem->id == @$item->id): ?> class="sidebar_active"<? endif ?>>
                <? foreach ($listFields as $field => $name): ?>
                  <td class="<?= $field ?>">
                    <a href="<?= $controller->url('edit', ['id' => $sidebarItem->id]) ?>">
                      <?= preg_match('/_html$/', $field) ? $sidebarItem->$field : htmlspecialchars($sidebarItem->$field) ?>
                    </a>
                  </td>
                <? endforeach ?>
              </tr>
            <? endforeach ?>
            <tr>
              <td colspan="<?= count($listFields) + 1 ?>" class="buttons">
                <a href="<?= $controller->url('add', ['category' => $categoryValue]) ?>" class="button">Add <?= $noun ?></a>
              </td>
            </tr>
        </tbody>
      </table>
      <br>
    <? endforeach ?>
  </div>

  <div class="content">
    <? if ($crudContentTpl): ?>
      <? View::renderTpl($crudContentTpl, get_defined_vars()) ?>
    <? else: ?>

    <? endif ?>
  </div>
</div>