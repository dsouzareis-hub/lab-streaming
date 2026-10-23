<?php
declare(strict_types=1);

Auth::require('movie.create');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Http::redirect('/colaborador/novo');
}
$url = (string) ($_POST['youtube_url'] ?? '');
$id = Youtube::extractId($url);
View::render('collab/preview', [
    'id' => $id,
    'embed' => $id ? Youtube::embedUrl($id) : null,
    'available' => $id ? Youtube::available($id) : false,
], 'Pré-visualização');
