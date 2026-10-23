<h1>Meu perfil</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<form class="form" method="post" action="/conta">
  <?= Csrf::field() ?>
  <?php foreach ($errors as $er): ?><div class="flash err" role="alert"><?= e($er) ?></div><?php endforeach; ?>
  <?php if ($me['avatar']): ?><img src="/uploads/<?= e($me['avatar']) ?>" alt="Sua foto de perfil" width="96" height="96" style="border-radius:50%"><?php endif; ?>
  <label for="name">Nome</label><input id="name" name="name" value="<?= e($me['name']) ?>" required maxlength="80">
  <label for="email">E-mail</label><input id="email" value="<?= e($me['email']) ?>" disabled>
  <p>Perfil: <span class="badge"><?= e($me['role']) ?></span></p>
  <p><button>Salvar</button></p>
</form>
