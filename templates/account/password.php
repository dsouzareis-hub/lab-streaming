<h1>Alterar senha</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<form class="form" method="post" action="/conta/senha">
  <?= Csrf::field() ?>
  <?php foreach ($errors as $er): ?><div class="flash err" role="alert"><?= e($er) ?></div><?php endforeach; ?>
  <label for="current">Senha atual</label><input id="current" type="password" name="current" required autocomplete="current-password">
  <label for="password">Nova senha</label><input id="password" type="password" name="password" required minlength="10" autocomplete="new-password">
  <label for="password2">Confirmar</label><input id="password2" type="password" name="password2" required minlength="10" autocomplete="new-password">
  <p><button>Alterar</button></p>
</form>
