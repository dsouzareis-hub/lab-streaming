<?php
declare(strict_types=1);

$me = Auth::require('profile.manage');
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $errors = Validator::clean([Validator::name($name)]);
    if (!$errors) {
        Db::exec('UPDATE users SET name=? WHERE id=?', [$name, $me['id']]);
        Audit::log('profile.update');
        Session::flash('ok', 'Perfil atualizado.');
        Http::redirect('/conta');
    }
}
View::render('account/profile', ['me' => $me, 'errors' => $errors], 'Meu perfil');
