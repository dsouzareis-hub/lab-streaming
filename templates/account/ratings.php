<h1>Minhas avaliações</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<?php if (!$rows): ?><p class="empty">Você ainda não avaliou nenhum filme.</p><?php else: ?>
<table><thead><tr><th>Filme</th><th>Nota</th><th>Comentário</th><th>Data</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
  <tr><td><a href="/filme?id=<?= (int) $r['id'] ?>"><?= e($r['title']) ?></a></td><td>★ <?= (int) $r['stars'] ?></td>
  <td><?= e($r['comment']) ?></td><td><?= e($r['created_at']) ?></td></tr>
<?php endforeach; ?></tbody></table>
<?php endif; ?>
