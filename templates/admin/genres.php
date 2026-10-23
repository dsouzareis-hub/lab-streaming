<h1>Gêneros</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<form class="form" method="post" action="/admin/generos">
  <?= Csrf::field() ?><input type="hidden" name="do" value="add">
  <label for="name">Novo gênero</label><input id="name" name="name" required maxlength="60">
  <p><button>Adicionar</button></p>
</form>
<br>
<table><thead><tr><th>Nome</th><th>Filmes</th><th></th></tr></thead><tbody>
<?php foreach ($genres as $g): ?>
  <tr><td><?= e($g['name']) ?></td><td><?= (int) $g['n'] ?></td><td>
    <form class="inline" method="post" action="/admin/generos" data-confirm="Excluir este gênero?"><?= Csrf::field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int) $g['id'] ?>"><button class="danger">Excluir</button></form>
  </td></tr>
<?php endforeach; ?></tbody></table>
