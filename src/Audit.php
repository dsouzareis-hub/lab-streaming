<?php
declare(strict_types=1);

final class Audit
{
    public static function log(string $action, string $detail = '', ?int $uid = null): void
    {
        $uid ??= $_SESSION['uid'] ?? null;
        try {
            Db::exec(
                'INSERT INTO audit_log (user_id,action,detail,ip) VALUES (?,?,?,?)',
                [$uid, $action, mb_substr($detail, 0, 500), Http::ip()]
            );
        } catch (Throwable $e) {
            error_log('audit failure: ' . $e->getMessage());
        }
    }
}
