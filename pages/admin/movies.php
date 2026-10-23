<?php
declare(strict_types=1);

Auth::require('movie.edit');
$page = max(1, (int) ($_GET['p'] ?? 1));
$per = 30;
View::render('admin/movies', [
    'movies' => Db::all(
        'SELECT m.*, g.name AS genre_name, u.name AS author FROM movies m
         JOIN genres g ON g.id=m.genre_id LEFT JOIN users u ON u.id=m.created_by
         ORDER BY m.created_at DESC, m.id DESC LIMIT ' . $per . ' OFFSET ' . (($page - 1) * $per)
    ),
    'page' => $page, 'pages' => max(1, (int) ceil(Movie::count(true) / $per)),
], 'Gerenciar filmes');
