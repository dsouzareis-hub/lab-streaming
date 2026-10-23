<?php
declare(strict_types=1);

Auth::require('audit.read');
View::render('admin/audit', [
    'rows' => Db::all(
        'SELECT a.*, u.email FROM audit_log a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.id DESC LIMIT 200'
    ),
], 'Auditoria');
