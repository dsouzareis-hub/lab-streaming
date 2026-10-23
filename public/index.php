<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = '/' . trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// rota => arquivo em pages/ (as páginas ficam fora da raiz pública)
$routes = [
    '/'                  => 'home',
    '/catalogo'          => 'catalog',
    '/genero'            => 'genre',
    '/busca'             => 'search',
    '/filme'             => 'movie',
    '/login'             => 'login',
    '/logout'            => 'logout',
    '/cadastro'          => 'register',
    '/recuperar-senha'   => 'forgot',
    '/redefinir-senha'   => 'reset',
    '/contato'           => 'contact',
    '/sobre'             => 'about',
    '/privacidade'       => 'privacy',
    '/termos'            => 'terms',
    '/conta'             => 'account/profile',
    '/conta/senha'       => 'account/password',
    '/conta/foto'        => 'account/avatar',
    '/conta/favoritos'   => 'account/favorites',
    '/conta/avaliacoes'  => 'account/ratings',
    '/conta/historico'   => 'account/history',
    '/conta/excluir'     => 'account/delete',
    '/acao/favorito'     => 'actions/favorite',
    '/acao/avaliar'      => 'actions/rate',
    '/acao/denunciar'    => 'actions/report',
    '/conta/denuncias'   => 'account/complaints',
    '/conta/denuncia'    => 'account/complaint',
    '/colaborador'       => 'collab/dashboard',
    '/colaborador/novo'  => 'collab/create',
    '/colaborador/previa' => 'collab/preview',
    '/colaborador/importar' => 'collab/import',
    '/admin'             => 'admin/dashboard',
    '/admin/filmes'      => 'admin/movies',
    '/admin/filmes/novo' => 'admin/movie_form',
    '/admin/filmes/editar' => 'admin/movie_form',
    '/admin/filmes/acao' => 'admin/movie_action',
    '/admin/usuarios'    => 'admin/users',
    '/admin/usuarios/novo' => 'admin/user_form',
    '/admin/usuarios/acao' => 'admin/user_action',
    '/admin/generos'     => 'admin/genres',
    '/admin/mensagens'   => 'admin/messages',
    '/admin/avaliacoes'  => 'admin/reports_queue',
    '/admin/relatorios'  => 'admin/reports',
    '/admin/auditoria'   => 'admin/audit',
];

if (!isset($routes[$path])) {
    Http::abort(404, 'Página não encontrada.');
}
if ($method === 'POST') {
    Csrf::check();
}
$page = BASE_PATH . '/pages/' . $routes[$path] . '.php';
if (!is_file($page)) {
    Http::abort(404, 'Página não encontrada.');
}
require $page;
