<?php
declare(strict_types=1);

$me = Auth::require('movie.create');
$errors = [];
$old = ['title' => '', 'synopsis' => '', 'genre_id' => '', 'age_rating' => 'L', 'release_year' => '', 'youtube_url' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $_) {
        $old[$k] = trim((string) ($_POST[$k] ?? ''));
    }
    [$errors, $data] = Movie::validate($old);
    if (!$errors && Youtube::available($data['youtube_id']) === false) {
        $errors[] = 'O vídeo está indisponível (removido, privado ou com incorporação desativada).';
    }
    if (!$errors) {
        $id = Db::insert(
            'INSERT INTO movies (title,synopsis,genre_id,age_rating,release_year,youtube_id,created_by) VALUES (?,?,?,?,?,?,?)',
            [$data['title'], $data['synopsis'], $data['genre_id'], $data['age_rating'], $data['release_year'], $data['youtube_id'], $me['id']]
        );
        Audit::log('movie.create', "#$id " . $data['title']);
        Session::flash('ok', 'Filme cadastrado.');
        Http::redirect($me['role'] === 'admin' ? '/admin/filmes' : '/colaborador');
    }
}
View::render('collab/create', [
    'errors' => $errors, 'old' => $old, 'genres' => Db::all('SELECT * FROM genres ORDER BY name'),
    'action' => '/colaborador/novo', 'heading' => 'Cadastrar filme',
], 'Cadastrar filme');
