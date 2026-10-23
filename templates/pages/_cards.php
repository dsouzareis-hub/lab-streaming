<?php if (!$movies): ?>
  <p class="empty">Nenhum filme encontrado.</p>
<?php else: ?>
  <div class="grid">
    <?php foreach ($movies as $m): ?>
      <a class="card" href="/filme?id=<?= (int) $m['id'] ?>">
        <img src="<?= e(Youtube::thumb($m['youtube_id'])) ?>" alt="Capa de <?= e($m['title']) ?>" loading="lazy">
        <div class="meta"><strong><?= e($m['title']) ?></strong><br>
          <small><?= e($m['genre_name']) ?> · <?= e($m['age_rating']) ?><?= $m['avg_stars'] ? ' · ★ ' . e((string) $m['avg_stars']) : '' ?></small></div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
