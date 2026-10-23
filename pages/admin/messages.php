<?php
declare(strict_types=1);

Auth::require('message.read');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Db::exec('DELETE FROM contact_messages WHERE id=?', [(int) ($_POST['id'] ?? 0)]);
    Audit::log('message.delete', '#' . (int) ($_POST['id'] ?? 0));
    Session::flash('ok', 'Mensagem excluída.');
    Http::redirect('/admin/mensagens');
}
View::render('admin/messages', ['rows' => Db::all('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 200')], 'Mensagens');
