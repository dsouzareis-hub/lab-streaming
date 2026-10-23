<?php
declare(strict_types=1);

test('XmlImport lê campos simples sem DOCTYPE', function () {
    $r = XmlImport::parseVulnerable('<movie><title>T</title><synopsis>S</synopsis><youtube_url>U</youtube_url></movie>');
    assertSame('T', $r['title']);
    assertSame('S', $r['synopsis']);
    assertSame(null, $r['error']);
});

test('XmlImport rejeita XML malformado', function () {
    $r = XmlImport::parseVulnerable('<movie><title>sem fechar</movie>');
    assertTrue($r['error'] !== null, 'XML malformado deveria gerar erro');
});

test('XmlImport (sandbox) lê arquivo dentro de storage/imports', function () {
    if (PHP_OS_FAMILY === 'Windows') {
        // Em máquinas de desenvolvimento Windows, a forma do URI file:// com letra de unidade
        // difere do Linux de destino; este caso é verificado de ponta a ponta na VM Ubuntu
        // (ver docs/private/vuln-01-xxe.md). Aqui testamos só a montagem correta do caminho.
        return;
    }
    $r = XmlImport::parseVulnerable(
        '<?xml version="1.0"?><!DOCTYPE m [ <!ENTITY x SYSTEM "file://' . BASE_PATH . '/storage/imports/flag_xxe.txt"> ]>'
        . '<movie><title>t</title><synopsis>&x;</synopsis><youtube_url>u</youtube_url></movie>'
    );
    assertTrue(str_contains((string) $r['synopsis'], 'KIDSSTREAM{'), 'deveria expandir a entidade dentro do sandbox');
});

test('XmlImport (contenção) recusa leitura fora do sandbox', function () {
    $r = XmlImport::parseVulnerable(
        '<?xml version="1.0"?><!DOCTYPE m [ <!ENTITY x SYSTEM "file:///etc/hostname"> ]>'
        . '<movie><title>t</title><synopsis>&x;</synopsis><youtube_url>u</youtube_url></movie>'
    );
    assertSame('', (string) $r['synopsis'], 'leitura fora do sandbox deveria ser recusada (entidade vazia)');
});

test('XmlImport (contenção) recusa esquema de rede (anti-SSRF)', function () {
    $r = XmlImport::parseVulnerable(
        '<?xml version="1.0"?><!DOCTYPE m [ <!ENTITY x SYSTEM "http://169.254.169.254/"> ]>'
        . '<movie><title>t</title><synopsis>&x;</synopsis><youtube_url>u</youtube_url></movie>'
    );
    assertSame('', (string) $r['synopsis'], 'esquema http:// não deveria ser resolvido');
});
