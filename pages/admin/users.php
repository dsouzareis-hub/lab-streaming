<?php
declare(strict_types=1);

$me = Auth::require('user.manage');
View::render('admin/users', [
    'me' => $me,
    'users' => Db::all('SELECT id,name,email,role,active,created_at FROM users ORDER BY created_at DESC LIMIT 200'),
], 'Usuários');
