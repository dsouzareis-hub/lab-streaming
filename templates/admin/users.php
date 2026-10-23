<h1>Usuários</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<p><a class="btn" href="/admin/usuarios/novo">Criar conta</a></p>
<table><thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Estado</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($users as $u): ?>
  <tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td>
  <td>
    <form class="inline" method="post" action="/admin/usuarios/acao"><?= Csrf::field() ?>
      <input type="hidden" name="id" value="<?= (int) $u['id'] ?>"><input type="hidden" name="do" value="role">
      <select name="role" aria-label="Perfil de <?= e($u['name']) ?>">
        <?php foreach (['client' => 'Cliente', 'collaborator' => 'Colaborador', 'admin' => 'Administrador'] as $v => $l): ?>
          <option value="<?= $v ?>" <?= $u['role'] === $v ? 'selected' : '' ?>><?= $l ?></option>
        <?php endforeach; ?>
      </select> <button class="alt">Aplicar</button>
    </form>
  </td>
  <td><?= $u['active'] ? 'Ativo' : 'Desativado' ?></td>
  <td>
    <?php if ((int) $u['id'] !== (int) $me['id']): ?>
      <form class="inline" method="post" action="/admin/usuarios/acao"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $u['id'] ?>"><input type="hidden" name="do" value="<?= $u['active'] ? 'deactivate' : 'activate' ?>"><button class="alt"><?= $u['active'] ? 'Desativar' : 'Ativar' ?></button></form>
      <form class="inline" method="post" action="/admin/usuarios/acao" data-confirm="Excluir esta conta definitivamente?"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $u['id'] ?>"><input type="hidden" name="do" value="delete"><button class="danger">Excluir</button></form>
    <?php else: ?>(você)<?php endif; ?>
  </td></tr>
<?php endforeach; ?></tbody></table>
