<?php
declare(strict_types=1);

// Verifica a disponibilidade dos trailers do seed via oEmbed oficial. Uso: php scripts/check_videos.php
if (PHP_SAPI !== 'cli') {
    exit(1);
}
require dirname(__DIR__) . '/src/bootstrap.php';
$bad = 0;
foreach (require __DIR__ . '/seed_movies.php' as $m) {
    $r = Youtube::available($m[5]);
    echo str_pad($m[0], 40) . $m[5] . '  ' . ($r === true ? 'OK' : ($r === false ? 'INDISPONÍVEL' : 'SEM VERIFICAÇÃO')) . PHP_EOL;
    $bad += $r === false ? 1 : 0;
}
exit($bad ? 1 : 0);
