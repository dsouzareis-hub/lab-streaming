<h1>Excluir conta</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<form class="form" method="post" action="/conta/excluir" data-confirm="Excluir definitivamente sua conta?">
  <?= Csrf::field() ?>
  <?php if ($error): ?><div class="flash err" role="alert"><?= e($error) ?></div><?php endif; ?>
  <p>Esta ação é permanente. Favoritos, avaliações e histórico serão removidos.</p>
  <label for="password">Senha</label><input id="password" type="password" name="password" required autocomplete="current-password">
  <label for="confirm">Digite EXCLUIR</label><input id="confirm" name="confirm" required autocomplete="off">
  <p><button class="danger">Excluir minha conta</button></p>
</form>
