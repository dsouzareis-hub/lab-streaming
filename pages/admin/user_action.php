<?php
declare(strict_types=1);

$me = Auth::require('user.manage');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Http::abort(405, 'Método não permitido.');
}
$id = (int) ($_POST['id'] ?? 0);
$do = (string) ($_POST['do'] ?? '');
$target = Db::one('SELECT * FROM users WHERE id=?', [$id]);
if (!$target) {
    Http::abort(404, 'Usuário não encontrado.');
}
$admins = static fn (): int => (int) Db::one('SELECT COUNT(*) c FROM users WHERE role="admin" AND active=1')['c'];
$isLastAdmin = $target['role'] === 'admin' && $target['active'] && $admins() <= 1;

switch ($do) {
    case 'role':
        $role = (string) ($_POST['role'] ?? '');
        if (!in_array($role, ['client', 'collaborator', 'admin'], true)) {
            Http::abort(400, 'Perfil inválido.');
        }
        if ($isLastAdmin && $role !== 'admin') {
            Session::flash('err', 'Não é possível rebaixar o único administrador.');
            break;
        }
        Db::exec('UPDATE users SET role=? WHERE id=?', [$role, $id]);
        Session::flash('ok', 'Perfil atualizado.');
        break;
    case 'activate':
    case 'deactivate':
        if ($id === (int) $me['id'] || ($do === 'deactivate' && $isLastAdmin)) {
            Session::flash('err', 'Operação não permitida.');
            break;
        }
        Db::exec('UPDATE users SET active=? WHERE id=?', [$do === 'activate' ? 1 : 0, $id]);
        Session::flash('ok', 'Estado atualizado.');
        break;
    case 'delete':
        if ($id === (int) $me['id'] || $isLastAdmin) {
            Session::flash('err', 'Operação não permitida.');
            break;
        }
        Avatar::delete($target['avatar']);
        Db::exec('DELETE FROM users WHERE id=?', [$id]);
        Session::flash('ok', 'Conta excluída.');
        break;
    default:
        Http::abort(400, 'Ação inválida.');
}
Audit::log('user.' . $do, "#$id");
Http::redirect('/admin/usuarios');
