<?php $title = $title ?? 'Erro'; ?><!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Erro · Kids Stream</title><link rel="stylesheet" href="/assets/css/app.css"></head>
<body><main><div class="hero"><h1><?= e((string) http_response_code()) ?></h1>
<p><?= e($title !== '' ? $title : 'Ocorreu um erro.') ?></p><a class="btn" href="/">Voltar ao início</a></div></main></body></html>
