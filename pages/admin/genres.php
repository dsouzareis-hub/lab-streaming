<?php
declare(strict_types=1);

Auth::require('genre.manage');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do = (string) ($_POST['do'] ?? '');
    if ($do === 'add') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: '')), '-');
        if (mb_strlen($name) < 2 || mb_strlen($name) > 60 || $slug === '') {
            Session::flash('err', 'Nome de gênero inválido.');
        } elseif (Db::one('SELECT id FROM genres WHERE name=? OR slug=?', [$name, $slug])) {
            Session::flash('err', 'Gênero já existe.');
        } else {
            Db::exec('INSERT INTO genres (name,slug) VALUES (?,?)', [$name, $slug]);
            Audit::log('genre.add', $name);
            Session::flash('ok', 'Gênero criado.');
        }
    } elseif ($do === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ((int) Db::one('SELECT COUNT(*) c FROM movies WHERE genre_id=?', [$id])['c'] > 0) {
            Session::flash('err', 'Há filmes neste gênero; mova-os antes de excluir.');
        } else {
            Db::exec('DELETE FROM genres WHERE id=?', [$id]);
            Audit::log('genre.delete', "#$id");
            Session::flash('ok', 'Gênero excluído.');
        }
    }
    Http::redirect('/admin/generos');
}
View::render('admin/genres', [
    'genres' => Db::all('SELECT g.*, (SELECT COUNT(*) FROM movies m WHERE m.genre_id=g.id) AS n FROM genres g ORDER BY g.name'),
], 'Gêneros');
