<h1>Busca</h1>
<?php if ($q === ''): ?>
  <p class="empty">Digite um termo para buscar filmes.</p>
<?php else: ?>
  <p>Resultados para “<?= e($q) ?>”:</p>
  <?php require __DIR__ . '/_cards.php'; ?>
<?php endif; ?>
