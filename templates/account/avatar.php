<h1>Foto de perfil</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<div class="form">
  <?php if ($error): ?><div class="flash err" role="alert"><?= e($error) ?></div><?php endif; ?>
  <?php if ($me['avatar']): ?><img src="/uploads/<?= e($me['avatar']) ?>" alt="Sua foto atual" width="128" height="128" style="border-radius:50%"><?php endif; ?>
  <form method="post" action="/conta/foto" enctype="multipart/form-data">
    <?= Csrf::field() ?>
    <label for="avatar">Imagem (JPEG, PNG ou WebP, até 2 MB)</label>
    <input id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required>
    <p><button>Enviar</button></p>
  </form>
  <?php if ($me['avatar']): ?>
    <form method="post" action="/conta/foto"><?= Csrf::field() ?><input type="hidden" name="remove" value="1"><button class="alt">Remover foto</button></form>
  <?php endif; ?>
</div>
