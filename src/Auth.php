<?php
declare(strict_types=1);

final class Auth
{
    /** Matriz de permissões explícita: ação => perfis autorizados. */
    public const PERMISSIONS = [
        'movie.view'      => ['client', 'collaborator', 'admin'],
        'favorite.manage' => ['client', 'admin'],
        'rating.manage'   => ['client', 'admin'],
        'profile.manage'  => ['client', 'collaborator', 'admin'],
        'movie.create'    => ['collaborator', 'admin'],
        'movie.edit'      => ['admin'],
        'movie.delete'    => ['admin'],
        'movie.hide'      => ['admin'],
        'genre.manage'    => ['admin'],
        'user.manage'     => ['admin'],
        'message.read'    => ['admin'],
        'audit.read'      => ['admin'],
        'report.read'     => ['admin'],
    ];

    private const DUMMY_HASH = '$2y$12$abcdefghijklmnopqrstuuG5gq9nQ1pJm0yKQ0r5r2q3r2q3r2q3r';

    private static array|null|false $cache = false;

    public static function user(): ?array
    {
        if (self::$cache !== false) {
            return self::$cache;
        }
        $id = $_SESSION['uid'] ?? null;
        self::$cache = $id
            ? Db::one('SELECT id,name,email,role,avatar FROM users WHERE id=? AND active=1', [$id])
            : null;
        return self::$cache;
    }

    public static function can(string $action): bool
    {
        $u = self::user();
        return $u !== null && in_array($u['role'], self::PERMISSIONS[$action] ?? [], true);
    }

    public static function require(string $action): array
    {
        $u = self::user();
        if ($u === null) {
            Http::redirect('/login');
        }
        if (!self::can($action)) {
            Audit::log('authz.denied', $action);
            Http::abort(403, 'Acesso negado.');
        }
        return $u;
    }

    public static function login(int $uid): void
    {
        session_regenerate_id(true);
        $_SESSION['uid'] = $uid;
        self::$cache = false;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
        self::$cache = false;
    }

    public static function attempt(string $email, string $pass): bool
    {
        $ip = Http::ip();
        if (RateLimit::tooMany($ip, $email)) {
            usleep(random_int(300000, 600000));
            return false;
        }
        $u = Db::one('SELECT * FROM users WHERE email=? AND active=1', [$email]);
        $locked = $u && $u['locked_until'] && strtotime($u['locked_until']) > time();
        $valid = password_verify($pass, $u['password_hash'] ?? self::DUMMY_HASH);
        if (!$u || !$valid || $locked) {
            RateLimit::fail($ip, $email);
            if ($u) {
                $n = (int) $u['failed_logins'] + 1;
                $until = $n >= 5 ? date('Y-m-d H:i:s', time() + min(900, 30 * 2 ** ($n - 5))) : null;
                Db::exec('UPDATE users SET failed_logins=?, locked_until=? WHERE id=?', [$n, $until, $u['id']]);
            }
            Audit::log('login.fail', $email);
            return false;
        }
        Db::exec('UPDATE users SET failed_logins=0, locked_until=NULL WHERE id=?', [$u['id']]);
        if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
            Db::exec('UPDATE users SET password_hash=? WHERE id=?', [password_hash($pass, PASSWORD_DEFAULT), $u['id']]);
        }
        self::login((int) $u['id']);
        Audit::log('login.ok', $email, (int) $u['id']);
        return true;
    }
}
