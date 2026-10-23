<h1><?= e($movie['title']) ?></h1>
<p><span class="badge"><?= e($movie['genre_name']) ?></span> <span class="badge">Classificação: <?= e($movie['age_rating']) ?></span>
  <?php if ($movie['release_year']): ?><span class="badge"><?= (int) $movie['release_year'] ?></span><?php endif; ?>
  <?php if ($movie['avg_stars']): ?><span class="badge">★ <?= e((string) $movie['avg_stars']) ?></span><?php endif; ?>
  <?php if ($movie['hidden']): ?><span class="badge">Oculto</span><?php endif; ?></p>
<div class="player">
  <iframe src="<?= e($embed) ?>" title="Trailer de <?= e($movie['title']) ?>" allow="encrypted-media; picture-in-picture; fullscreen" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
</div>
<p><small>Se o vídeo não carregar, ele pode ter sido removido, estar privado ou ter a incorporação desativada, ou pode faltar conexão com a internet.</small></p>
<h2>Sinopse</h2>
<p><?= nl2br(e($movie['synopsis'])) ?></p>

<?php if ($user && Auth::can('favorite.manage')): ?>
  <form action="/acao/favorito" method="post" class="inline"><?= Csrf::field() ?>
    <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
    <button><?= $fav ? '★ Remover dos favoritos' : '☆ Favoritar' ?></button>
  </form>
  <form action="/acao/avaliar" method="post" class="form">
    <?= Csrf::field() ?><input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
    <label for="stars">Sua nota</label>
    <select id="stars" name="stars">
      <?php for ($i = 5; $i >= 1; $i--): ?>
        <option value="<?= $i ?>" <?= ($mine['stars'] ?? 0) == $i ? 'selected' : '' ?>><?= $i ?> estrela(s)</option>
      <?php endfor; ?>
    </select>
    <label for="comment">Comentário (opcional)</label>
    <textarea id="comment" name="comment" maxlength="500"><?= e($mine['comment'] ?? '') ?></textarea>
    <p><button>Salvar avaliação</button></p>
  </form>
<?php elseif (!$user): ?>
  <p><a href="/login">Entre</a> para favoritar e avaliar.</p>
<?php endif; ?>

<h2>Avaliações</h2>
<?php if (!$reviews): ?><p class="empty">Ainda não há avaliações com comentário.</p><?php endif; ?>
<?php foreach ($reviews as $r): ?>
  <p><strong><?= e($r['name']) ?></strong> — ★ <?= (int) $r['stars'] ?><br><?= e($r['comment']) ?>
  <?php if ($user && Auth::can('complaint.create') && (int) $r['user_id'] !== (int) $user['id']): ?>
    <details><summary>Denunciar</summary>
      <form method="post" action="/acao/denunciar"><?= Csrf::field() ?>
        <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
        <input type="hidden" name="rated_user_id" value="<?= (int) $r['user_id'] ?>">
        <label for="reason-<?= (int) $r['user_id'] ?>">Motivo</label>
        <textarea id="reason-<?= (int) $r['user_id'] ?>" name="reason" maxlength="500" required minlength="5"></textarea>
        <button class="alt small">Enviar denúncia</button>
      </form>
    </details>
  <?php endif; ?>
  </p>
<?php endforeach; ?>
