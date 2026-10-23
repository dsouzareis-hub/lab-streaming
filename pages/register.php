<?php
declare(strict_types=1);

$errors = [];
$old = ['name' => '', 'email' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = ['name' => trim((string) ($_POST['name'] ?? '')), 'email' => strtolower(trim((string) ($_POST['email'] ?? '')))];
    $pass = (string) ($_POST['password'] ?? '');
    $errors = Validator::clean([
        Validator::name($old['name']), Validator::email($old['email']), Validator::password($pass),
        $pass !== (string) ($_POST['password2'] ?? '') ? 'As senhas não coincidem.' : null,
        empty($_POST['terms']) ? 'É necessário aceitar os termos de uso.' : null,
    ]);
    if (!$errors && Db::one('SELECT id FROM users WHERE email=?', [$old['email']])) {
        $errors[] = 'Não foi possível criar a conta com estes dados.';
    }
    if (!$errors) {
        $id = Db::insert(
            'INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,"client")',
            [$old['name'], $old['email'], password_hash($pass, PASSWORD_DEFAULT)]
        );
        Audit::log('user.register', $old['email'], $id);
        Auth::login($id);
        Session::flash('ok', 'Conta criada com sucesso!');
        Http::redirect('/conta');
    }
}
View::render('pages/register', ['errors' => $errors, 'old' => $old], 'Criar conta');
