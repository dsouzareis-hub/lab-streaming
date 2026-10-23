<?php
declare(strict_types=1);

final class Config
{
    private static array $v = [];

    public static function load(string $file): void
    {
        if (!is_readable($file)) {
            return;
        }
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$k, $val] = explode('=', $line, 2);
            self::$v[trim($k)] = trim($val, " \t\"'");
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::$v[$key] ?? $default;
    }
}
