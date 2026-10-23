<?php
declare(strict_types=1);

Auth::require('user.manage');
$errors = [];
$old = ['name' => '', 'email' => '', 'role' => 'client'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'email' => strtolower(trim((string) ($_POST['email'] ?? ''))),
        'role' => (string) ($_POST['role'] ?? ''),
    ];
    $pass = (string) ($_POST['password'] ?? '');
    $errors = Validator::clean([
        Validator::name($old['name']), Validator::email($old['email']), Validator::password($pass),
        in_array($old['role'], ['client', 'collaborator', 'admin'], true) ? null : 'Perfil inválido.',
    ]);
    if (!$errors && Db::one('SELECT id FROM users WHERE email=?', [$old['email']])) {
        $errors[] = 'Já existe uma conta com este e-mail.';
    }
    if (!$errors) {
        $id = Db::insert(
            'INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?)',
            [$old['name'], $old['email'], password_hash($pass, PASSWORD_DEFAULT), $old['role']]
        );
        Audit::log('user.create', "#$id " . $old['role']);
        Session::flash('ok', 'Conta criada.');
        Http::redirect('/admin/usuarios');
    }
}
View::render('admin/user_form', ['errors' => $errors, 'old' => $old], 'Criar conta');
