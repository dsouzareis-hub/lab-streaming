<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle !== '' ? $pageTitle . ' · Kids Stream' : 'Kids Stream') ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
  <script src="/assets/js/app.js" defer></script>
</head>
<body>
<a class="skip" href="#conteudo">Ir para o conteúdo</a>
<header class="topbar">
  <a class="brand" href="/">Kids<span>Stream</span></a>
  <nav aria-label="Principal" class="nav">
    <a href="/catalogo">Catálogo</a>
    <a href="/sobre">Sobre</a>
    <a href="/contato">Contato</a>
  </nav>
  <form class="search" action="/busca" method="get" role="search">
    <input type="search" name="q" placeholder="Buscar filmes" aria-label="Buscar filmes" maxlength="80">
  </form>
  <nav aria-label="Conta" class="nav">
    <?php if ($user): ?>
      <?php if ($user['role'] === 'admin'): ?><a href="/admin">Admin</a><?php endif; ?>
      <?php if ($user['role'] === 'collaborator'): ?><a href="/colaborador">Painel</a><?php endif; ?>
      <a href="/conta"><?= e($user['name']) ?></a>
      <form action="/logout" method="post" class="inline"><?= Csrf::field() ?><button class="link">Sair</button></form>
    <?php else: ?>
      <a href="/login">Entrar</a>
      <a class="btn small" href="/cadastro">Criar conta</a>
    <?php endif; ?>
  </nav>
</header>
<main id="conteudo">
  <?php foreach ($flashes as [$type, $msg]): ?>
    <div class="flash <?= e($type) ?>" role="status"><?= e($msg) ?></div>
  <?php endforeach; ?>
  <?= $content ?>
</main>
<footer class="foot">
  <p>© Kids Stream — plataforma fictícia de laboratório.</p>
  <p><a href="/privacidade">Privacidade</a> · <a href="/termos">Termos</a> · <a href="/contato">Contato</a></p>
</footer>
</body>
</html>
