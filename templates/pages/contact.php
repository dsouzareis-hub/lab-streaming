<h1>Contato</h1>
<form class="form" method="post" action="/contato">
  <?= Csrf::field() ?>
  <?php foreach ($errors as $er): ?><div class="flash err" role="alert"><?= e($er) ?></div><?php endforeach; ?>
  <label for="name">Nome</label><input id="name" name="name" value="<?= e($old['name']) ?>" required maxlength="80">
  <label for="email">E-mail</label><input id="email" type="email" name="email" value="<?= e($old['email']) ?>" required maxlength="190">
  <label for="subject">Assunto</label><input id="subject" name="subject" value="<?= e($old['subject']) ?>" required maxlength="120">
  <label for="body">Mensagem</label><textarea id="body" name="body" required maxlength="3000"><?= e($old['body']) ?></textarea>
  <p><button>Enviar</button></p>
</form>
