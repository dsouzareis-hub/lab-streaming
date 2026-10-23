<h1>Registros de auditoria</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<?php if (!$rows): ?><p class="empty">Sem registros.</p><?php else: ?>
<table><thead><tr><th>Data</th><th>Usuário</th><th>Ação</th><th>Detalhe</th><th>IP</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
  <tr><td><?= e($r['created_at']) ?></td><td><?= e($r['email'] ?? '—') ?></td><td><?= e($r['action']) ?></td><td><?= e($r['detail']) ?></td><td><?= e($r['ip']) ?></td></tr>
<?php endforeach; ?></tbody></table>
<?php endif; ?>
