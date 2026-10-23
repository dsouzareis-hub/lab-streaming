<?php
declare(strict_types=1);

$me = Auth::require('movie.create');
$mine = Db::all(
    'SELECT m.id, m.title, m.hidden, m.created_at, g.name AS genre_name
     FROM movies m JOIN genres g ON g.id=m.genre_id WHERE m.created_by=? ORDER BY m.created_at DESC',
    [$me['id']]
);
View::render('collab/dashboard', ['mine' => $mine], 'Painel do colaborador');
