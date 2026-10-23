<?php
declare(strict_types=1);

$me = Auth::require('profile.manage');
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $row = Db::one('SELECT password_hash, role FROM users WHERE id=?', [$me['id']]);
    if (!password_verify((string) ($_POST['password'] ?? ''), $row['password_hash'])) {
        $error = 'Senha incorreta.';
    } elseif (($_POST['confirm'] ?? '') !== 'EXCLUIR') {
        $error = 'Digite EXCLUIR para confirmar.';
    } elseif ($row['role'] === 'admin' && (int) Db::one('SELECT COUNT(*) c FROM users WHERE role="admin" AND active=1')['c'] <= 1) {
        $error = 'Não é possível excluir o único administrador.';
    } else {
        Audit::log('user.self_delete', $me['email']);
        Avatar::delete($me['avatar']);
        Db::exec('DELETE FROM users WHERE id=?', [$me['id']]);
        Auth::logout();
        Session::flash('ok', 'Conta excluída.');
        Http::redirect('/');
    }
}
View::render('account/delete', ['error' => $error], 'Excluir conta');
