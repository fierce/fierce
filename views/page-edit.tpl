<? namespace Fierce ?>

<? if ($formType == 'Add'): ?>
  <h1>Add <?= $noun ?></h1>
<? else: ?>
  <h1>Edit <?= htmlspecialchars($item->name) ?></h1>
<? endif ?>

<? View::form([
  'action' => $formType == 'Add' ? $controller->url('add-submit') : $controller->url('edit-submit', ['id' => $item->id]),
  'class' => 'medium',
  'onsubmit' => "if (document.getElementById('name_field').value == '') {alert('name is a required field.'); return false; }",
  'data' => $item
]) ?>

  <? 
    if ($item->admin_category == 'main') {
      View::field([
        'type' => 'hidden',
        'name' => 'nav_position',
        'value' => ($formType == 'Add') ? '100000' : $item->nav_position
      ]);
    } else if ($item->admin_category == 'not_linked') {
      View::field(['type' => 'hidden', 'name' => 'is_user_page', 'value' => true]);
    }
  ?>

  <? View::fieldRow([
    'name' => 'name'
  ]) ?>
  
  <? View::fieldRow([
    'name' => 'url',
    'class' => 'large'
  ]) ?>
  
  <? View::selectRow([
    'name' => 'class',
    'options' => $classOptions
  ]) ?>
  
  <? View::textareaRow([
    'name' => 'content',
    'class' => 'wysiwyg full'
  ]) ?>
  
  <div class="buttons">
    <a href="javascript:void(0)" confirmedHref="<?= $controller->url('delete', ['id' => $item->id]) ?>" onclick="
          if (confirm('Are you sure you want to delete this page?')) {
            this.setAttribute('href', this.getAttribute('confirmedHref'))
          }
        " class="grey" style="float: left;">Delete this Page</a>
    <input type="submit" value="Save <?= $noun ?>">
  </div>
<? View::closeForm() ?>
