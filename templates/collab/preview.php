<h1>Pré-visualização do trailer</h1>
<?php if (!$id): ?>
  <div class="flash err" role="alert">URL inválida. Use links youtube.com/watch?v=, youtu.be/ ou youtube.com/embed/.</div>
<?php else: ?>
  <?php if ($available === false): ?><div class="flash err" role="alert">O vídeo parece indisponível (removido, privado ou sem incorporação).</div>
  <?php elseif ($available === null): ?><div class="flash" role="status">Não foi possível verificar a disponibilidade agora (sem conexão?). Confira a prévia abaixo.</div><?php endif; ?>
  <div class="player"><iframe src="<?= e($embed) ?>" title="Prévia do trailer" allow="encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe></div>
<?php endif; ?>
<p><a class="btn alt" href="/colaborador/novo">Voltar ao formulário</a></p>
