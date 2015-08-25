<? namespace Fierce ?>

<? $pageTitle = 'Log In' ?>

<h1>Log In</h1>

<? if ($message): ?>

<p class="message"><?= htmlspecialchars($message); ?></p>

<? endif ?>

<? View::form([
  'action' => 'login?do=submit',
  'class' => 'medium',
  'data' => $loginData
]) ?>
  <? View::field([
    'name' => 'return',
    'type' => 'hidden'
  ]) ?>
  
  <? View::fieldRow([
    'name' => 'email',
  ]) ?>
  <? View::fieldRow([
    'name' => 'password',
    'type' => 'password'
  ]) ?>

  <div class="buttons">
    <input type="submit" value="Continue">
  </div>
<? View::closeForm() ?>

<script type="text/javascript">
  document.getElementById('email_field').focus()
</script>
