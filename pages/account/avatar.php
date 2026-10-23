<?php
declare(strict_types=1);

$me = Auth::require('profile.manage');
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['remove'])) {
        Avatar::delete($me['avatar']);
        Db::exec('UPDATE users SET avatar=NULL WHERE id=?', [$me['id']]);
        Audit::log('avatar.remove');
        Session::flash('ok', 'Foto removida.');
        Http::redirect('/conta/foto');
    }
    [$name, $error] = Avatar::store($_FILES['avatar'] ?? []);
    if ($name) {
        Avatar::delete($me['avatar']);
        Db::exec('UPDATE users SET avatar=? WHERE id=?', [$name, $me['id']]);
        Audit::log('avatar.update');
        Session::flash('ok', 'Foto atualizada.');
        Http::redirect('/conta/foto');
    }
}
View::render('account/avatar', ['me' => $me, 'error' => $error], 'Foto de perfil');
