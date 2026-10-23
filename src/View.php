<?php
declare(strict_types=1);

final class View
{
    public static function render(string $view, array $data = [], string $title = ''): void
    {
        $file = BASE_PATH . '/templates/' . $view . '.php';
        if (!preg_match('#^[a-z_/]+$#', $view) || !is_file($file)) {
            Http::abort(404, 'Página não encontrada.');
        }
        extract($data, EXTR_SKIP);
        $user = Auth::user();
        $flashes = Session::takeFlash();
        $pageTitle = $title;
        ob_start();
        require $file;
        $content = ob_get_clean();
        require BASE_PATH . '/templates/layout/main.php';
    }
}
