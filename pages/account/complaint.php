<?php
declare(strict_types=1);

/**
 * Vulnerabilidade intencional 2 — Broken Access Control (IDOR).
 *
 * Autorização esperada: apenas o autor da denúncia (reporter_id) ou um administrador podem
 * visualizar o detalhe de uma denúncia específica.
 *
 * Comportamento vulnerável: o controlador verifica apenas a permissão de perfil
 * (Auth::require('complaint.view') — cliente ou admin), mas NÃO verifica se o registro
 * pertence ao usuário autenticado. Qualquer cliente autenticado pode ler qualquer denúncia
 * incrementando o parâmetro "id" na URL.
 *
 * Gabarito completo: docs/private/vuln-02-bac.md (fora do repositório público).
 */
$me = Auth::require('complaint.view');
$id = (int) ($_GET['id'] ?? 0);
$row = Db::one(
    'SELECT r.*, m.title AS movie_title, u.name AS rated_name, ru.name AS reporter_name
     FROM reports r JOIN movies m ON m.id=r.movie_id JOIN users u ON u.id=r.rated_user_id
     JOIN users ru ON ru.id=r.reporter_id WHERE r.id=?',
    [$id]
);
if (!$row) {
    Http::abort(404, 'Denúncia não encontrada.');
}
View::render('account/complaint', ['row' => $row], 'Denúncia #' . $id);
