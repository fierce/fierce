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
      View::field(['type' => 'hidden', 'name' => 'nav_position', 'value' => '100000']);
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
  
  <script type="text/javascript" src="fierce/third-party/ckeditor/ckeditor.js"></script>  

  
  <div class="buttons">
    <input type="submit" value="Save <?= $noun ?>">
  </div>
<? View::closeForm() ?>
