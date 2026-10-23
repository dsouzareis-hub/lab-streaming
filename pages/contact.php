<?php
declare(strict_types=1);

$errors = [];
$old = ['name' => '', 'email' => '', 'subject' => '', 'body' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $_) {
        $old[$k] = trim((string) ($_POST[$k] ?? ''));
    }
    $errors = Validator::clean([
        Validator::name($old['name']), Validator::email($old['email']),
        (mb_strlen($old['subject']) < 3 || mb_strlen($old['subject']) > 120) ? 'Assunto deve ter de 3 a 120 caracteres.' : null,
        (mb_strlen($old['body']) < 10 || mb_strlen($old['body']) > 3000) ? 'Mensagem deve ter de 10 a 3000 caracteres.' : null,
    ]);
    if (!$errors) {
        $recent = (int) Db::one('SELECT COUNT(*) c FROM audit_log WHERE action="contact.send" AND ip=? AND created_at>DATE_SUB(NOW(), INTERVAL 10 MINUTE)', [Http::ip()])['c'];
        if ($recent >= 5) {
            $errors[] = 'Muitas mensagens enviadas. Tente mais tarde.';
        }
    }
    if (!$errors) {
        Db::insert('INSERT INTO contact_messages (name,email,subject,body) VALUES (?,?,?,?)', array_values($old));
        Audit::log('contact.send');
        Session::flash('ok', 'Mensagem enviada. Obrigado!');
        Http::redirect('/contato');
    }
}
View::render('pages/contact', ['errors' => $errors, 'old' => $old], 'Contato');
