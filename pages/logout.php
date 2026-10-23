<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Audit::log('logout');
    Auth::logout();
}
Http::redirect('/');
