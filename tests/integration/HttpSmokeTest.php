<?php
declare(strict_types=1);

/** Testes de integração HTTP contra uma instância em execução (ver tests/run.php / APP_URL). */
$base = rtrim((string) getenv('APP_URL'), '/');

$get = static function (string $path) use ($base): array {
    $ctx = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 10, 'follow_location' => 0]]);
    $body = @file_get_contents($base . $path, false, $ctx);
    $status = 0;
    foreach ($http_response_header ?? [] as $h) {
        if (preg_match('#^HTTP/\S+\s+(\d+)#', $h, $m)) {
            $status = (int) $m[1];
        }
    }
    return [$status, (string) $body];
};

test('GET / responde 200', function () use ($get) {
    [$status] = $get('/');
    assertSame(200, $status);
});

test('GET /login responde 200', function () use ($get) {
    [$status] = $get('/login');
    assertSame(200, $status);
});

test('GET /admin sem sessão redireciona para login (302)', function () use ($get) {
    [$status] = $get('/admin');
    assertSame(302, $status);
});

test('GET /config/.env não é servido pelo Apache', function () use ($get) {
    [$status] = $get('/config/.env');
    assertTrue($status !== 200, 'config/.env não deveria ser acessível via HTTP');
});

test('GET /src/Db.php não é servido pelo Apache', function () use ($get) {
    [$status] = $get('/src/Db.php');
    assertTrue($status !== 200, 'código-fonte não deveria ser acessível via HTTP');
});

test('GET /rota-inexistente responde 404', function () use ($get) {
    [$status] = $get('/rota-inexistente-xyz');
    assertSame(404, $status);
});

test('Cabeçalhos de segurança presentes em /', function () use ($base) {
    $ctx = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 10, 'follow_location' => 0]]);
    @file_get_contents($base . '/', false, $ctx);
    $headers = implode("\n", $http_response_header ?? []);
    assertTrue(str_contains($headers, 'X-Frame-Options'), 'X-Frame-Options ausente');
    assertTrue(str_contains($headers, 'Content-Security-Policy'), 'Content-Security-Policy ausente');
});
