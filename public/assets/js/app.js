// Confirmação de ações destrutivas (CSP bloqueia handlers inline)
document.addEventListener('submit', function (ev) {
  var msg = ev.target.getAttribute('data-confirm');
  if (msg && !window.confirm(msg)) {
    ev.preventDefault();
  }
});
