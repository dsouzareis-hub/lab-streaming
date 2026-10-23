<h1>Redefinir senha</h1>
<form class="form" method="post" action="/redefinir-senha">
  <?= Csrf::field() ?>
  <input type="hidden" name="token" value="<?= e($token) ?>">
  <?php foreach ($errors as $er): ?><div class="flash err" role="alert"><?= e($er) ?></div><?php endforeach; ?>
  <label for="password">Nova senha</label>
  <input id="password" type="password" name="password" required minlength="10" maxlength="200" autocomplete="new-password">
  <label for="password2">Confirmar</label>
  <input id="password2" type="password" name="password2" required minlength="10" maxlength="200" autocomplete="new-password">
  <p><button>Redefinir</button></p>
</form>
