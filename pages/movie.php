<?php
declare(strict_types=1);

$user = Auth::user();
$isAdmin = $user && $user['role'] === 'admin';
$movie = Movie::find((int) ($_GET['id'] ?? 0), $isAdmin);
if (!$movie) {
    Http::abort(404, 'Filme não encontrado.');
}
$fav = false;
$mine = null;
if ($user) {
    $fav = (bool) Db::one('SELECT 1 FROM favorites WHERE user_id=? AND movie_id=?', [$user['id'], $movie['id']]);
    $mine = Db::one('SELECT * FROM ratings WHERE user_id=? AND movie_id=?', [$user['id'], $movie['id']]);
    if (!$movie['hidden']) {
        Db::exec('INSERT INTO watch_history (user_id,movie_id) VALUES (?,?)', [$user['id'], $movie['id']]);
    }
}
$reviews = Db::all(
    'SELECT r.stars, r.comment, r.created_at, u.id AS user_id, u.name FROM ratings r JOIN users u ON u.id=r.user_id
     WHERE r.movie_id=? AND r.comment IS NOT NULL AND r.comment<>"" ORDER BY r.created_at DESC LIMIT 20',
    [$movie['id']]
);
View::render('pages/movie', [
    'movie' => $movie, 'fav' => $fav, 'mine' => $mine, 'reviews' => $reviews,
    'embed' => Youtube::embedUrl($movie['youtube_id']),
], $movie['title']);
