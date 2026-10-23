<h1><?= e($heading) ?></h1>
<form class="form" method="post" action="<?= e($action) ?>">
  <?= Csrf::field() ?>
  <?php if (!empty($movieId)): ?><input type="hidden" name="id" value="<?= (int) $movieId ?>"><?php endif; ?>
  <?php foreach ($errors as $er): ?><div class="flash err" role="alert"><?= e($er) ?></div><?php endforeach; ?>
  <label for="title">Título</label><input id="title" name="title" value="<?= e($old['title']) ?>" required maxlength="160">
  <label for="synopsis">Sinopse</label><textarea id="synopsis" name="synopsis" required maxlength="4000"><?= e($old['synopsis']) ?></textarea>
  <label for="genre_id">Gênero</label>
  <select id="genre_id" name="genre_id" required>
    <option value="">Selecione…</option>
    <?php foreach ($genres as $g): ?><option value="<?= (int) $g['id'] ?>" <?= (string) $old['genre_id'] === (string) $g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option><?php endforeach; ?>
  </select>
  <label for="age_rating">Classificação indicativa</label>
  <select id="age_rating" name="age_rating">
    <?php foreach (['L' => 'Livre', '10' => '10 anos', '12' => '12 anos'] as $v => $l): ?><option value="<?= $v ?>" <?= (string) $old['age_rating'] === (string) $v ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
  </select>
  <label for="release_year">Ano de lançamento</label><input id="release_year" name="release_year" value="<?= e((string) $old['release_year']) ?>" inputmode="numeric" maxlength="4">
  <label for="youtube_url">URL do trailer (YouTube)</label>
  <input id="youtube_url" name="youtube_url" value="<?= e($old['youtube_url']) ?>" required maxlength="200" placeholder="https://www.youtube.com/watch?v=...">
  <p><button formaction="/colaborador/previa" formnovalidate class="alt">Pré-visualizar trailer</button> <button>Salvar</button></p>
</form>
