<h1>Catálogo</h1>
<div class="subnav"><?php foreach ($genres as $g): ?>
  <a class="badge" href="/genero?slug=<?= e($g['slug']) ?>"><?= e($g['name']) ?></a>
<?php endforeach; ?></div>
<?php require __DIR__ . '/_cards.php'; ?>
<?php if ($pages > 1): ?>
  <p class="subnav">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a class="btn small <?= $i === $page ? '' : 'alt' ?>" href="/catalogo?p=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
  </p>
<?php endif; ?>
