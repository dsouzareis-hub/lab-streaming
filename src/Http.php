<?php
declare(strict_types=1);

final class Http
{
    public static function securityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header("Content-Security-Policy: default-src 'self'; img-src 'self' https://i.ytimg.com data:; "
            . "frame-src https://www.youtube-nocookie.com; style-src 'self'; script-src 'self'; "
            . "base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
    }

    public static function redirect(string $path): never
    {
        if (!str_starts_with($path, '/') || str_starts_with($path, '//') || str_contains($path, '\\')) {
            $path = '/';
        }
        header('Location: ' . $path);
        exit;
    }

    public static function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public static function abort(int $code, string $title = ''): never
    {
        http_response_code($code);
        require BASE_PATH . '/templates/pages/error.php';
        exit;
    }
}
