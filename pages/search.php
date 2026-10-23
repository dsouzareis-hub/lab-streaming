<?php
declare(strict_types=1);

$q = trim((string) ($_GET['q'] ?? ''));
$q = mb_substr($q, 0, 80);
View::render('pages/search', ['q' => $q, 'movies' => $q === '' ? [] : Movie::search($q)], 'Busca');
