<?php
declare(strict_types=1);

final class Csrf
{
    public static function token(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function check(): void
    {
        $sent = (string) ($_POST['_csrf'] ?? '');
        if (!hash_equals(self::token(), $sent)) {
            Http::abort(403, 'Requisição inválida (CSRF).');
        }
    }
}
