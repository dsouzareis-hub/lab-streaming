<?php
declare(strict_types=1);

$me = Auth::require('profile.manage');
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cur = (string) ($_POST['current'] ?? '');
    $new = (string) ($_POST['password'] ?? '');
    $row = Db::one('SELECT password_hash FROM users WHERE id=?', [$me['id']]);
    $errors = Validator::clean([
        password_verify($cur, $row['password_hash']) ? null : 'Senha atual incorreta.',
        Validator::password($new),
        $new !== (string) ($_POST['password2'] ?? '') ? 'As senhas não coincidem.' : null,
    ]);
    if (!$errors) {
        Db::exec('UPDATE users SET password_hash=? WHERE id=?', [password_hash($new, PASSWORD_DEFAULT), $me['id']]);
        session_regenerate_id(true);
        Audit::log('password.change');
        Session::flash('ok', 'Senha alterada.');
        Http::redirect('/conta');
    }
}
View::render('account/password', ['errors' => $errors], 'Alterar senha');
