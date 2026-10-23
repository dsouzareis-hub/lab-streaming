<h1>Mensagens de contato</h1>
<?php require __DIR__ . '/_nav.php'; ?>
<?php if (!$rows): ?><p class="empty">Nenhuma mensagem recebida.</p><?php endif; ?>
<?php foreach ($rows as $m): ?>
  <article class="stat" style="margin-bottom:12px">
    <strong><?= e($m['subject']) ?></strong> <small>— <?= e($m['name']) ?> &lt;<?= e($m['email']) ?>&gt; · <?= e($m['created_at']) ?></small>
    <p><?= nl2br(e($m['body'])) ?></p>
    <form method="post" action="/admin/mensagens" data-confirm="Excluir mensagem?"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><button class="danger">Excluir</button></form>
  </article>
<?php endforeach; ?>
