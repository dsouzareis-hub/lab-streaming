<h1>Importar filmes (XML)</h1>
<p>Recurso em avaliação: cole metadados em XML exportados de outro catálogo para pré-visualizar os campos antes do cadastro manual.</p>
<form class="form" method="post" action="/colaborador/importar">
  <?= Csrf::field() ?>
  <label for="xml">XML de metadados</label>
  <textarea id="xml" name="xml" rows="10" maxlength="200000" placeholder="&lt;movie&gt;&lt;title&gt;...&lt;/title&gt;&lt;synopsis&gt;...&lt;/synopsis&gt;&lt;youtube_url&gt;...&lt;/youtube_url&gt;&lt;/movie&gt;" required></textarea>
  <p><button>Pré-visualizar</button></p>
</form>
<?php if ($result): ?>
  <h2>Resultado</h2>
  <?php if ($result['error']): ?>
    <div class="flash err" role="alert">Não foi possível processar: <?= e($result['error']) ?></div>
  <?php else: ?>
    <table><tbody>
      <tr><th>Título</th><td><?= e($result['title'] ?? '') ?></td></tr>
      <tr><th>Sinopse</th><td><?= nl2br(e($result['synopsis'] ?? '')) ?></td></tr>
      <tr><th>URL do trailer</th><td><?= e($result['youtube_url'] ?? '') ?></td></tr>
    </tbody></table>
    <p><small>A importação automática para o catálogo ainda não está disponível; use o <a href="/colaborador/novo">cadastro manual</a>.</small></p>
  <?php endif; ?>
<?php endif; ?>
