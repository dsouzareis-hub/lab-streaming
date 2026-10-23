<h1>Denúncia #<?= (int) $row['id'] ?></h1>
<?php require __DIR__ . '/_nav.php'; ?>
<table><tbody>
  <tr><th>Filme</th><td><?= e($row['movie_title']) ?></td></tr>
  <tr><th>Avaliação denunciada de</th><td><?= e($row['rated_name']) ?></td></tr>
  <tr><th>Denunciado por</th><td><?= e($row['reporter_name']) ?></td></tr>
  <tr><th>Motivo</th><td><?= e($row['reason']) ?></td></tr>
  <tr><th>Status</th><td><?= e($row['status']) ?></td></tr>
  <tr><th>Data</th><td><?= e($row['created_at']) ?></td></tr>
</tbody></table>
<p><a class="btn alt" href="/conta/denuncias">Voltar</a></p>
