<?php
declare(strict_types=1);

$token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$row = preg_match('/^[a-f0-9]{64}$/', $token)
    ? Db::one('SELECT * FROM password_resets WHERE token_hash=? AND used=0 AND expires_at>NOW()', [hash('sha256', $token)])
    : null;
$errors = [];
if (!$row) {
    Session::flash('err', 'Link inválido ou expirado.');
    Http::redirect('/recuperar-senha');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = (string) ($_POST['password'] ?? '');
    $errors = Validator::clean([
        Validator::password($pass),
        $pass !== (string) ($_POST['password2'] ?? '') ? 'As senhas não coincidem.' : null,
    ]);
    if (!$errors) {
        $pdo = Db::pdo();
        $pdo->beginTransaction();
        Db::exec('UPDATE users SET password_hash=?, failed_logins=0, locked_until=NULL WHERE id=?', [password_hash($pass, PASSWORD_DEFAULT), $row['user_id']]);
        Db::exec('UPDATE password_resets SET used=1 WHERE user_id=?', [$row['user_id']]);
        $pdo->commit();
        Audit::log('password.reset.done', '', (int) $row['user_id']);
        Session::flash('ok', 'Senha redefinida. Faça login.');
        Http::redirect('/login');
    }
}
View::render('pages/reset', ['errors' => $errors, 'token' => $token], 'Redefinir senha');
