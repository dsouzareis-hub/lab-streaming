<?php
declare(strict_types=1);

// Seed idempotente com dados fictícios. Uso (CLI): php scripts/seed.php [arquivo-de-credenciais]
// Senhas das contas de demonstração são geradas aleatoriamente e gravadas no arquivo indicado (modo 600).
if (PHP_SAPI !== 'cli') {
    exit(1);
}
require dirname(__DIR__) . '/src/bootstrap.php';

$genres = [
    'Animação' => 'animacao', 'Aventura' => 'aventura', 'Comédia' => 'comedia',
    'Fantasia' => 'fantasia', 'Musical' => 'musical', 'Família' => 'familia',
];
foreach ($genres as $name => $slug) {
    Db::exec('INSERT IGNORE INTO genres (name,slug) VALUES (?,?)', [$name, $slug]);
}
$gid = static fn (string $n): int => (int) Db::one('SELECT id FROM genres WHERE name=?', [$n])['id'];

// título, sinopse, gênero, classificação, ano, ID do trailer no YouTube
$movies = require __DIR__ . '/seed_movies.php';
foreach ($movies as [$title, $syn, $genre, $age, $year, $yt]) {
    if (!Db::one('SELECT id FROM movies WHERE title=?', [$title])) {
        Db::exec(
            'INSERT INTO movies (title,synopsis,genre_id,age_rating,release_year,youtube_id) VALUES (?,?,?,?,?,?)',
            [$title, $syn, $gid($genre), $age, $year, $yt]
        );
    }
}

$creds = [];
$users = [
    ['Administrador Lab', 'admin@kidsstream.lab', 'admin'],
    ['Colaboradora Lab', 'colaborador@kidsstream.lab', 'collaborator'],
    ['Cliente Um', 'cliente1@kidsstream.lab', 'client'],
    ['Cliente Dois', 'cliente2@kidsstream.lab', 'client'],
];
foreach ($users as [$name, $email, $role]) {
    if (Db::one('SELECT id FROM users WHERE email=?', [$email])) {
        continue;
    }
    $pass = rtrim(strtr(base64_encode(random_bytes(12)), '+/', 'Ab'), '=') . '9';
    Db::exec(
        'INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?)',
        [$name, $email, password_hash($pass, PASSWORD_DEFAULT), $role]
    );
    $creds[] = "$role\t$email\t$pass";
}

$out = $argv[1] ?? null;
if ($creds && $out) {
    file_put_contents($out, implode("\n", $creds) . "\n", FILE_APPEND);
    chmod($out, 0600);
}
echo 'Seed concluído. Contas novas: ' . count($creds) . PHP_EOL;
