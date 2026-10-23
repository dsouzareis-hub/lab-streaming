<section class="hero">
  <h1>Trailers para a criançada 🎬</h1>
  <p>Descubra os melhores trailers de filmes infantis, salve favoritos e avalie.</p>
  <a class="btn" href="/catalogo">Explorar catálogo</a>
</section>
<h2>Novidades</h2>
<?php $movies = $latest; require __DIR__ . '/_cards.php'; ?>
<?php foreach ($sections as $s): ?>
  <h2><a href="/genero?slug=<?= e($s['genre']['slug']) ?>"><?= e($s['genre']['name']) ?></a></h2>
  <?php $movies = $s['movies']; require __DIR__ . '/_cards.php'; ?>
<?php endforeach; ?>
