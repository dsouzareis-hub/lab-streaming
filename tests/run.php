<?php
declare(strict_types=1);

/**
 * Executor de testes minimalista, sem dependências externas (sem Composer/PHPUnit).
 * Uso: php tests/run.php
 * Variável opcional: APP_URL=http://192.168.3.30 php tests/run.php  (ativa os testes de integração HTTP)
 */
if (PHP_SAPI !== 'cli') {
    exit(1);
}

require dirname(__DIR__) . '/src/bootstrap.php';

$total = 0;
$failed = 0;

/** @param callable $fn */
function test(string $name, callable $fn): void
{
    global $total, $failed;
    $total++;
    try {
        $fn();
        echo "  ok  {$name}\n";
    } catch (Throwable $e) {
        $failed++;
        echo "FAIL  {$name}: {$e->getMessage()}\n";
    }
}

function assertTrue(bool $cond, string $msg): void
{
    if (!$cond) {
        throw new RuntimeException($msg);
    }
}

function assertSame(mixed $expected, mixed $actual, string $msg = ''): void
{
    if ($expected !== $actual) {
        $e = var_export($expected, true);
        $a = var_export($actual, true);
        throw new RuntimeException("{$msg} (esperado {$e}, obtido {$a})");
    }
}

foreach (glob(__DIR__ . '/unit/*.php') as $file) {
    echo basename($file) . ":\n";
    require $file;
}

$appUrl = getenv('APP_URL');
if ($appUrl !== false && $appUrl !== '') {
    foreach (glob(__DIR__ . '/integration/*.php') as $file) {
        echo basename($file) . " (contra {$appUrl}):\n";
        require $file;
    }
} else {
    echo "(testes de integração HTTP ignorados: defina APP_URL para ativá-los)\n";
}

echo "\n{$total} teste(s), {$failed} falha(s)\n";
exit($failed > 0 ? 1 : 0);
