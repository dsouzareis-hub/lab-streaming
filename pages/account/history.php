<?php
declare(strict_types=1);

$me = Auth::require('profile.manage');
$rows = Db::all(
    'SELECT m.id, m.title, MAX(h.watched_at) AS last_watch, COUNT(*) AS times
     FROM watch_history h JOIN movies m ON m.id=h.movie_id WHERE h.user_id=? AND m.hidden=0
     GROUP BY m.id, m.title ORDER BY last_watch DESC LIMIT 50',
    [$me['id']]
);
View::render('account/history', ['rows' => $rows], 'Histórico');
