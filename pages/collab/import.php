<?php
declare(strict_types=1);

Auth::require('movie.import');
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xml = (string) ($_POST['xml'] ?? '');
    if (trim($xml) === '') {
        Session::flash('err', 'Cole o XML de metadados.');
    } else {
        $result = XmlImport::parseVulnerable($xml);
    }
}
View::render('collab/import', ['result' => $result], 'Importar filmes (XML)');
