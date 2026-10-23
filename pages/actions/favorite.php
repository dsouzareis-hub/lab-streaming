<?php
declare(strict_types=1);

$me = Auth::require('favorite.manage');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Http::abort(405, 'Método não permitido.');
}
$id = (int) ($_POST['movie_id'] ?? 0);
if (!Movie::find($id)) {
    Http::abort(404, 'Filme não encontrado.');
}
if (Db::one('SELECT 1 FROM favorites WHERE user_id=? AND movie_id=?', [$me['id'], $id])) {
    Db::exec('DELETE FROM favorites WHERE user_id=? AND movie_id=?', [$me['id'], $id]);
    Session::flash('ok', 'Removido dos favoritos.');
} else {
    Db::exec('INSERT INTO favorites (user_id,movie_id) VALUES (?,?)', [$me['id'], $id]);
    Session::flash('ok', 'Adicionado aos favoritos.');
}
Http::redirect('/filme?id=' . $id);
