<h1>Recuperar senha</h1>
<form class="form" method="post" action="/recuperar-senha">
  <?= Csrf::field() ?>
  <label for="email">E-mail da conta</label>
  <input id="email" type="email" name="email" required maxlength="190" autocomplete="email">
  <p><button>Enviar instruções</button></p>
</form>
