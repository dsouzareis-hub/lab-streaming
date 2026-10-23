<h1>Filmes</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<p><a class="btn" href="/admin/filmes/novo">Novo filme</a></p>
<?php if (!$movies): ?><p class="empty">Nenhum filme cadastrado.</p><?php else: ?>
<table><thead><tr><th>Título</th><th>Gênero</th><th>Autor</th><th>Estado</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($movies as $m): ?>
  <tr><td><a href="/filme?id=<?= (int) $m['id'] ?>"><?= e($m['title']) ?></a></td><td><?= e($m['genre_name']) ?></td><td><?= e($m['author'] ?? '—') ?></td>
  <td><?= $m['hidden'] ? 'Oculto' : 'Visível' ?></td>
  <td>
    <a class="btn small alt" href="/admin/filmes/editar?id=<?= (int) $m['id'] ?>">Editar</a>
    <form class="inline" method="post" action="/admin/filmes/acao"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><input type="hidden" name="do" value="<?= $m['hidden'] ? 'show' : 'hide' ?>"><button class="alt small"><?= $m['hidden'] ? 'Reexibir' : 'Ocultar' ?></button></form>
    <form class="inline" method="post" action="/admin/filmes/acao" data-confirm="Excluir este filme definitivamente?"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><input type="hidden" name="do" value="delete"><button class="danger">Excluir</button></form>
  </td></tr>
<?php endforeach; ?></tbody></table>
<?php if ($pages > 1): ?><p class="subnav"><?php for ($i = 1; $i <= $pages; $i++): ?><a class="btn small <?= $i === $page ? '' : 'alt' ?>" href="/admin/filmes?p=<?= $i ?>"><?= $i ?></a><?php endfor; ?></p><?php endif; ?>
<?php endif; ?>
