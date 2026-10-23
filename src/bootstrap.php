<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    $file = BASE_PATH . '/src/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/src/helpers.php';

Config::load(BASE_PATH . '/config/.env');

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');
error_reporting(E_ALL);

Session::start();
Http::securityHeaders();

set_exception_handler(function (Throwable $e): void {
    error_log((string) $e);
    http_response_code(500);
    $title = 'Erro interno. Tente novamente mais tarde.';
    require BASE_PATH . '/templates/pages/error.php';
});
