<h1>Histórico de visualização</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<?php if (!$rows): ?><p class="empty">Nenhum filme assistido ainda.</p><?php else: ?>
<table><thead><tr><th>Filme</th><th>Última vez</th><th>Vezes</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
  <tr><td><a href="/filme?id=<?= (int) $r['id'] ?>"><?= e($r['title']) ?></a></td><td><?= e($r['last_watch']) ?></td><td><?= (int) $r['times'] ?></td></tr>
<?php endforeach; ?></tbody></table>
<?php endif; ?>
