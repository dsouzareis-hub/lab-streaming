<h1>Criar conta</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<form class="form" method="post" action="/admin/usuarios/novo">
  <?= Csrf::field() ?>
  <?php foreach ($errors as $er): ?><div class="flash err" role="alert"><?= e($er) ?></div><?php endforeach; ?>
  <label for="name">Nome</label><input id="name" name="name" value="<?= e($old['name']) ?>" required maxlength="80">
  <label for="email">E-mail</label><input id="email" type="email" name="email" value="<?= e($old['email']) ?>" required maxlength="190">
  <label for="role">Perfil</label>
  <select id="role" name="role">
    <?php foreach (['client' => 'Cliente', 'collaborator' => 'Colaborador', 'admin' => 'Administrador'] as $v => $l): ?>
      <option value="<?= $v ?>" <?= $old['role'] === $v ? 'selected' : '' ?>><?= $l ?></option>
    <?php endforeach; ?>
  </select>
  <label for="password">Senha inicial (mín. 10, letras e números)</label>
  <input id="password" type="password" name="password" required minlength="10" autocomplete="new-password">
  <p><button>Criar</button></p>
</form>
