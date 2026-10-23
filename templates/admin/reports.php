<h1>Relatórios</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<h2>Filmes por gênero</h2>
<table><thead><tr><th>Gênero</th><th>Filmes</th></tr></thead><tbody><?php foreach ($byGenre as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= (int) $r['n'] ?></td></tr><?php endforeach; ?></tbody></table>
<h2>Mais bem avaliados</h2>
<?php if (!$top): ?><p class="empty">Sem avaliações.</p><?php else: ?>
<table><thead><tr><th>Filme</th><th>Média</th><th>Avaliações</th></tr></thead><tbody><?php foreach ($top as $r): ?><tr><td><?= e($r['title']) ?></td><td>★ <?= e((string) $r['avg']) ?></td><td><?= (int) $r['n'] ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?>
<h2>Mais favoritados</h2>
<?php if (!$favs): ?><p class="empty">Sem favoritos.</p><?php else: ?>
<table><thead><tr><th>Filme</th><th>Favoritos</th></tr></thead><tbody><?php foreach ($favs as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= (int) $r['n'] ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?>
<h2>Usuários por perfil</h2>
<table><thead><tr><th>Perfil</th><th>Total</th></tr></thead><tbody><?php foreach ($byRole as $r): ?><tr><td><?= e($r['role']) ?></td><td><?= (int) $r['n'] ?></td></tr><?php endforeach; ?></tbody></table>
