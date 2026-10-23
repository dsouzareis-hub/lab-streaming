<?php
declare(strict_types=1);

$me = Auth::require('complaint.create');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Http::abort(405, 'Método não permitido.');
}
$movieId = (int) ($_POST['movie_id'] ?? 0);
$ratedUserId = (int) ($_POST['rated_user_id'] ?? 0);
$reason = mb_substr(trim((string) ($_POST['reason'] ?? '')), 0, 500);
if (!Movie::find($movieId) || !Db::one('SELECT id FROM users WHERE id=?', [$ratedUserId]) || mb_strlen($reason) < 5) {
    Http::abort(400, 'Dados inválidos.');
}
$id = Db::insert(
    'INSERT INTO reports (reporter_id,rated_user_id,movie_id,reason) VALUES (?,?,?,?)',
    [$me['id'], $ratedUserId, $movieId, $reason]
);
Audit::log('report.create', "#$id movie#$movieId user#$ratedUserId");
Session::flash('ok', 'Denúncia enviada para análise.');
Http::redirect('/filme?id=' . $movieId);
