<? namespace F ?>

<? $pageTitle = $noun . ' List – ' . $formType ?>

<h1><?= $formType ?> <?= $noun ?></h1>

<? View::form([
  'action' => $formType == 'Add' ? $controller->url('add-submit') : $controller->url('edit-submit', ['id' => $item->id]),
  'class' => 'medium',
  'onsubmit' => "if (document.getElementById('name_field').value == '') {alert('name is a required field.'); return false; }",
  'data' => $item
]) ?>
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
