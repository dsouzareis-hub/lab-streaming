<h1>Painel do colaborador</h1>
<p>Você pode cadastrar novos filmes. Edição, ocultação e exclusão são feitas apenas pela administração.</p>
<p><a class="btn" href="/colaborador/novo">Cadastrar filme</a> <a class="btn alt" href="/colaborador/importar">Importar via XML</a></p>
<h2>Meus cadastros</h2>
<?php if (!$mine): ?><p class="empty">Você ainda não cadastrou filmes.</p><?php else: ?>
<table><thead><tr><th>Título</th><th>Gênero</th><th>Estado</th><th>Data</th></tr></thead><tbody>
<?php foreach ($mine as $m): ?>
  <tr><td><?= e($m['title']) ?></td><td><?= e($m['genre_name']) ?></td>
  <td><?= $m['hidden'] ? 'Oculto pela administração' : 'Publicado' ?></td><td><?= e($m['created_at']) ?></td></tr>
<?php endforeach; ?></tbody></table>
<?php endif; ?>
