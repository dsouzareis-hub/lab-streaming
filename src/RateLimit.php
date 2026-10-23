<?php
declare(strict_types=1);

final class RateLimit
{
    private const WINDOW = 900;

    public static function tooMany(string $ip, string $email): bool
    {
        $since = date('Y-m-d H:i:s', time() - self::WINDOW);
        $byIp = (int) Db::one('SELECT COUNT(*) c FROM login_attempts WHERE ip=? AND created_at>?', [$ip, $since])['c'];
        $byMail = (int) Db::one('SELECT COUNT(*) c FROM login_attempts WHERE email=? AND created_at>?', [$email, $since])['c'];
        return $byIp >= 20 || $byMail >= 10;
    }

    public static function fail(string $ip, string $email): void
    {
        Db::exec('INSERT INTO login_attempts (ip,email) VALUES (?,?)', [$ip, mb_substr($email, 0, 190)]);
    }
}
