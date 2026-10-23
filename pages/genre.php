<?php
declare(strict_types=1);

$slug = (string) ($_GET['slug'] ?? '');
$genre = Db::one('SELECT * FROM genres WHERE slug=?', [$slug]);
if (!$genre) {
    Http::abort(404, 'Gênero não encontrado.');
}
View::render('pages/genre', ['genre' => $genre, 'movies' => Movie::byGenre((int) $genre['id'])], $genre['name']);
