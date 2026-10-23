<?php
declare(strict_types=1);

final class Validator
{
    public static function name(string $v): ?string
    {
        $v = trim($v);
        return (mb_strlen($v) >= 2 && mb_strlen($v) <= 80 && !preg_match('/[<>\x00-\x1f]/', $v)) ? null : 'Nome deve ter de 2 a 80 caracteres, sem símbolos < ou >.';
    }

    public static function email(string $v): ?string
    {
        return (mb_strlen($v) <= 190 && filter_var($v, FILTER_VALIDATE_EMAIL)) ? null : 'E-mail inválido.';
    }

    public static function password(string $v): ?string
    {
        if (strlen($v) < 10 || strlen($v) > 200) {
            return 'A senha deve ter de 10 a 200 caracteres.';
        }
        if (!preg_match('/[A-Za-z]/', $v) || !preg_match('/\d/', $v)) {
            return 'A senha deve conter letras e números.';
        }
        return null;
    }

    public static function clean(array $errors): array
    {
        return array_values(array_filter($errors));
    }
}
