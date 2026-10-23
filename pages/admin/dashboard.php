<?php
declare(strict_types=1);

Auth::require('report.read');
$count = static fn (string $sql): int => (int) Db::one($sql)['c'];
View::render('admin/dashboard', ['s' => [
    'Filmes' => $count('SELECT COUNT(*) c FROM movies'),
    'Ocultos' => $count('SELECT COUNT(*) c FROM movies WHERE hidden=1'),
    'Usuários' => $count('SELECT COUNT(*) c FROM users'),
    'Colaboradores' => $count('SELECT COUNT(*) c FROM users WHERE role="collaborator"'),
    'Avaliações' => $count('SELECT COUNT(*) c FROM ratings'),
    'Mensagens' => $count('SELECT COUNT(*) c FROM contact_messages'),
]], 'Painel administrativo');
