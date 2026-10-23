<?php
declare(strict_types=1);

if (Auth::user()) {
    Http::redirect('/conta');
}
$error = null;
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $pass = (string) ($_POST['password'] ?? '');
    if (Auth::attempt($email, $pass)) {
        $role = Auth::user()['role'] ?? 'client';
        Http::redirect($role === 'admin' ? '/admin' : ($role === 'collaborator' ? '/colaborador' : '/conta'));
    }
    $error = 'E-mail ou senha incorretos, ou muitas tentativas. Tente novamente mais tarde.';
}
View::render('pages/login', ['error' => $error, 'email' => $email], 'Entrar');
