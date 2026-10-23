<h1>Entrar</h1>
<form class="form" method="post" action="/login">
  <?= Csrf::field() ?>
  <?php if ($error): ?><div class="flash err" role="alert"><?= e($error) ?></div><?php endif; ?>
  <label for="email">E-mail</label>
  <input id="email" type="email" name="email" value="<?= e($email) ?>" required maxlength="190" autocomplete="username">
  <label for="password">Senha</label>
  <input id="password" type="password" name="password" required maxlength="200" autocomplete="current-password">
  <p><button>Entrar</button></p>
  <p><a href="/recuperar-senha">Esqueci minha senha</a> · <a href="/cadastro">Criar conta</a></p>
</form>
