<?php
declare(strict_types=1);

$me = Auth::require('profile.manage');
$movies = Db::all(
    'SELECT m.*, g.name AS genre_name, g.slug AS genre_slug, NULL AS avg_stars
     FROM favorites f JOIN movies m ON m.id=f.movie_id JOIN genres g ON g.id=m.genre_id
     WHERE f.user_id=? AND m.hidden=0 ORDER BY f.created_at DESC',
    [$me['id']]
);
View::render('account/favorites', ['movies' => $movies], 'Favoritos');
