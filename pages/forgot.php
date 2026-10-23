<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $since = date('Y-m-d H:i:s', time() - 3600);
    $recent = (int) Db::one('SELECT COUNT(*) c FROM login_attempts WHERE ip=? AND email=? AND created_at>?', [Http::ip(), '[reset]' . $email, $since])['c'];
    if ($recent < 3 && Validator::email($email) === null) {
        RateLimit::fail(Http::ip(), '[reset]' . $email);
        $u = Db::one('SELECT id FROM users WHERE email=? AND active=1', [$email]);
        if ($u) {
            $token = bin2hex(random_bytes(32));
            Db::exec(
                'INSERT INTO password_resets (user_id,token_hash,expires_at) VALUES (?,?,DATE_ADD(NOW(), INTERVAL 30 MINUTE))',
                [$u['id'], hash('sha256', $token)]
            );
            // Sem SMTP no laboratório: o "e-mail" é gravado na caixa de saída local (storage/mail).
            $host = preg_replace('/[^A-Za-z0-9.\-:\[\]]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            file_put_contents(
                BASE_PATH . '/storage/mail/reset-' . $u['id'] . '-' . time() . '.txt',
                "Para: $email\nLink: http://$host/redefinir-senha?token=$token\nValido por 30 minutos.\n"
            );
            Audit::log('password.reset.request', $email, (int) $u['id']);
        }
    }
    Session::flash('ok', 'Se o e-mail existir, enviaremos as instruções de redefinição.');
    Http::redirect('/recuperar-senha');
}
View::render('pages/forgot', [], 'Recuperar senha');
