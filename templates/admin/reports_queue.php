<h1>Avaliações e denúncias</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<?php if (!$rows): ?><p class="empty">Nenhuma denúncia registrada.</p><?php else: ?>
<table><thead><tr><th>Filme</th><th>Denunciado</th><th>Autor</th><th>Motivo</th><th>Status</th><th>Ação</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
  <tr><td><?= e($r['movie_title']) ?></td><td><?= e($r['rated_name']) ?></td><td><?= e($r['reporter_name']) ?></td>
  <td><?= e($r['reason']) ?></td><td><?= e($r['status']) ?></td>
  <td><form class="inline" method="post" action="/admin/avaliacoes"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
    <select name="status" aria-label="Status da denúncia #<?= (int) $r['id'] ?>">
      <?php foreach (['pending' => 'Pendente', 'reviewed' => 'Revisada', 'dismissed' => 'Descartada'] as $v => $l): ?>
        <option value="<?= $v ?>" <?= $r['status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select> <button class="alt">Salvar</button></form></td></tr>
<?php endforeach; ?></tbody></table>
<?php endif; ?>
