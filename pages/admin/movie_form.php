<?php
declare(strict_types=1);

$me = Auth::require('movie.edit');
$isEdit = str_ends_with(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '', '/editar');
$movieId = $isEdit ? (int) ($_GET['id'] ?? $_POST['id'] ?? 0) : 0;
$existing = $isEdit ? Movie::find($movieId, true) : null;
if ($isEdit && !$existing) {
    Http::abort(404, 'Filme não encontrado.');
}
$old = $existing ? [
    'title' => $existing['title'], 'synopsis' => $existing['synopsis'], 'genre_id' => $existing['genre_id'],
    'age_rating' => $existing['age_rating'], 'release_year' => $existing['release_year'] ?? '',
    'youtube_url' => 'https://www.youtube.com/watch?v=' . $existing['youtube_id'],
] : ['title' => '', 'synopsis' => '', 'genre_id' => '', 'age_rating' => 'L', 'release_year' => '', 'youtube_url' => ''];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $_) {
        $old[$k] = trim((string) ($_POST[$k] ?? ''));
    }
    [$errors, $d] = Movie::validate($old);
    if (!$errors) {
        if ($existing) {
            Db::exec(
                'UPDATE movies SET title=?, synopsis=?, genre_id=?, age_rating=?, release_year=?, youtube_id=? WHERE id=?',
                [$d['title'], $d['synopsis'], $d['genre_id'], $d['age_rating'], $d['release_year'], $d['youtube_id'], $movieId]
            );
            Audit::log('movie.edit', "#$movieId");
        } else {
            $movieId = Db::insert(
                'INSERT INTO movies (title,synopsis,genre_id,age_rating,release_year,youtube_id,created_by) VALUES (?,?,?,?,?,?,?)',
                [$d['title'], $d['synopsis'], $d['genre_id'], $d['age_rating'], $d['release_year'], $d['youtube_id'], $me['id']]
            );
            Audit::log('movie.create', "#$movieId");
        }
        Session::flash('ok', 'Filme salvo.');
        Http::redirect('/admin/filmes');
    }
}
View::render('collab/create', [
    'errors' => $errors, 'old' => $old, 'genres' => Db::all('SELECT * FROM genres ORDER BY name'),
    'action' => $isEdit ? '/admin/filmes/editar?id=' . $movieId : '/admin/filmes/novo',
    'heading' => $isEdit ? 'Editar filme' : 'Novo filme', 'movieId' => $isEdit ? $movieId : null,
], $isEdit ? 'Editar filme' : 'Novo filme');
