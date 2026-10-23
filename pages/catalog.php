<?php
declare(strict_types=1);

$page = max(1, (int) ($_GET['p'] ?? 1));
$per = 24;
$total = Movie::count(false);
View::render('pages/catalog', [
    'movies' => Movie::all(false, $page, $per),
    'genres' => Db::all('SELECT * FROM genres ORDER BY name'),
    'page' => $page,
    'pages' => max(1, (int) ceil($total / $per)),
], 'Catálogo');
