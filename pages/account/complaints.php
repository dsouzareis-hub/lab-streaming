<?php
declare(strict_types=1);

$me = Auth::require('complaint.view');
$rows = Db::all(
    'SELECT r.id, r.reason, r.status, r.created_at, m.title FROM reports r JOIN movies m ON m.id=r.movie_id
     WHERE r.reporter_id=? ORDER BY r.created_at DESC',
    [$me['id']]
);
View::render('account/complaints', ['rows' => $rows], 'Minhas denúncias');
