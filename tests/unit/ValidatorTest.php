<?php
declare(strict_types=1);

test('Validator::name aceita nome válido', function () {
    assertSame(null, Validator::name('Maria Silva'));
});

test('Validator::name rejeita nome curto demais', function () {
    assertTrue(Validator::name('A') !== null, 'nome de 1 caractere deveria ser rejeitado');
});

test('Validator::name rejeita caracteres de marcação (anti-XSS básico)', function () {
    assertTrue(Validator::name('<script>') !== null, 'nome com "<" deveria ser rejeitado');
});

test('Validator::email aceita e-mail válido', function () {
    assertSame(null, Validator::email('user@example.com'));
});

test('Validator::email rejeita e-mail inválido', function () {
    assertTrue(Validator::email('não-é-email') !== null, 'e-mail inválido deveria ser rejeitado');
});

test('Validator::password rejeita senha curta', function () {
    assertTrue(Validator::password('abc123') !== null, 'senha curta deveria ser rejeitada');
});

test('Validator::password rejeita senha só com letras', function () {
    assertTrue(Validator::password('somenteletras') !== null, 'senha sem dígito deveria ser rejeitada');
});

test('Validator::password aceita senha com letras e números', function () {
    assertSame(null, Validator::password('SenhaForte123'));
});
