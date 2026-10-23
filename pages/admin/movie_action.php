<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Http::abort(405, 'Método não permitido.');
}
$id = (int) ($_POST['id'] ?? 0);
$do = (string) ($_POST['do'] ?? '');
$perm = ['hide' => 'movie.hide', 'show' => 'movie.hide', 'delete' => 'movie.delete'][$do] ?? null;
if ($perm === null) {
    Http::abort(400, 'Ação inválida.');
}
Auth::require($perm);
if (!Movie::find($id, true)) {
    Http::abort(404, 'Filme não encontrado.');
}
match ($do) {
    'hide' => Db::exec('UPDATE movies SET hidden=1 WHERE id=?', [$id]),
    'show' => Db::exec('UPDATE movies SET hidden=0 WHERE id=?', [$id]),
    'delete' => Db::exec('DELETE FROM movies WHERE id=?', [$id]),
};
Audit::log('movie.' . $do, "#$id");
Session::flash('ok', 'Operação concluída.');
Http::redirect('/admin/filmes');
