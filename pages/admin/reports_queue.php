<?php
declare(strict_types=1);

Auth::require('complaint.manage');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $status = (string) ($_POST['status'] ?? '');
    if (in_array($status, ['pending', 'reviewed', 'dismissed'], true)) {
        Db::exec('UPDATE reports SET status=? WHERE id=?', [$status, $id]);
        Audit::log('report.status', "#$id -> $status");
        Session::flash('ok', 'Status atualizado.');
    }
    Http::redirect('/admin/avaliacoes');
}
View::render('admin/reports_queue', [
    'rows' => Db::all(
        'SELECT r.*, m.title AS movie_title, u.name AS rated_name, ru.name AS reporter_name
         FROM reports r JOIN movies m ON m.id=r.movie_id JOIN users u ON u.id=r.rated_user_id
         JOIN users ru ON ru.id=r.reporter_id ORDER BY r.created_at DESC LIMIT 200'
    ),
], 'Avaliações e denúncias');
