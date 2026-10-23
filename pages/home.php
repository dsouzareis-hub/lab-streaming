<?php
declare(strict_types=1);

$genres = Db::all('SELECT * FROM genres ORDER BY name');
$sections = [];
foreach ($genres as $g) {
    $items = Movie::byGenre((int) $g['id'], 6);
    if ($items) {
        $sections[] = ['genre' => $g, 'movies' => $items];
    }
}
View::render('pages/home', ['latest' => Movie::latest(6), 'sections' => $sections], 'Início');
