<h1>Minhas denúncias</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<?php if (!$rows): ?><p class="empty">Você ainda não enviou denúncias.</p><?php else: ?>
<table><thead><tr><th>Filme</th><th>Status</th><th>Data</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
  <tr><td><?= e($r['title']) ?></td><td><?= e($r['status']) ?></td><td><?= e($r['created_at']) ?></td>
  <td><a class="btn small alt" href="/conta/denuncia?id=<?= (int) $r['id'] ?>">Ver</a></td></tr>
<?php endforeach; ?></tbody></table>
<?php endif; ?>
