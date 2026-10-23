<?php
declare(strict_types=1);

$me = Auth::require('rating.manage');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Http::abort(405, 'Método não permitido.');
}
$id = (int) ($_POST['movie_id'] ?? 0);
$stars = (int) ($_POST['stars'] ?? 0);
$comment = mb_substr(trim((string) ($_POST['comment'] ?? '')), 0, 500);
if (!Movie::find($id) || $stars < 1 || $stars > 5) {
    Http::abort(400, 'Dados inválidos.');
}
Db::exec(
    'INSERT INTO ratings (user_id,movie_id,stars,comment) VALUES (?,?,?,?)
     ON DUPLICATE KEY UPDATE stars=VALUES(stars), comment=VALUES(comment), created_at=NOW()',
    [$me['id'], $id, $stars, $comment === '' ? null : $comment]
);
Session::flash('ok', 'Avaliação salva.');
Http::redirect('/filme?id=' . $id);
