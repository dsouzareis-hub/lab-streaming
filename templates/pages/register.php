<h1>Criar conta</h1>
<form class="form" method="post" action="/cadastro">
  <?= Csrf::field() ?>
  <?php foreach ($errors as $er): ?><div class="flash err" role="alert"><?= e($er) ?></div><?php endforeach; ?>
  <label for="name">Nome</label>
  <input id="name" name="name" value="<?= e($old['name']) ?>" required maxlength="80" autocomplete="name">
  <label for="email">E-mail</label>
  <input id="email" type="email" name="email" value="<?= e($old['email']) ?>" required maxlength="190" autocomplete="email">
  <label for="password">Senha (mín. 10, letras e números)</label>
  <input id="password" type="password" name="password" required minlength="10" maxlength="200" autocomplete="new-password">
  <label for="password2">Confirmar senha</label>
  <input id="password2" type="password" name="password2" required minlength="10" maxlength="200" autocomplete="new-password">
  <label><input type="checkbox" name="terms" value="1" style="width:auto"> Li e aceito os <a href="/termos">Termos de uso</a></label>
  <p><button>Criar conta</button></p>
</form>
