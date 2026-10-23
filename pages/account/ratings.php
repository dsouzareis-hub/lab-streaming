<?php
declare(strict_types=1);

$me = Auth::require('profile.manage');
$rows = Db::all(
    'SELECT r.stars, r.comment, r.created_at, m.id, m.title FROM ratings r JOIN movies m ON m.id=r.movie_id
     WHERE r.user_id=? ORDER BY r.created_at DESC',
    [$me['id']]
);
View::render('account/ratings', ['rows' => $rows], 'Minhas avaliações');
