<?php
declare(strict_types=1);

final class Session
{
    public static function start(): void
    {
        session_name('KSID');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_start();
        if (isset($_SESSION['last']) && time() - $_SESSION['last'] > 3600) {
            $_SESSION = [];
            session_regenerate_id(true);
        }
        $_SESSION['last'] = time();
    }

    public static function flash(string $type, string $msg): void
    {
        $_SESSION['flash'][] = [$type, $msg];
    }

    public static function takeFlash(): array
    {
        $f = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $f;
    }
}
