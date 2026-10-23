<h1>Painel administrativo</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<div class="stats"><?php foreach ($s as $label => $n): ?>
  <div class="stat"><b><?= (int) $n ?></b><?= e($label) ?></div>
<?php endforeach; ?></div>
