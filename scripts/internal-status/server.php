<?php
declare(strict_types=1);

/**
 * Vulnerabilidade intencional 3 — Exposed Services.
 *
 * Painel interno fictício de status, deixado acessível sem autenticação em uma porta separada
 * do site principal (ver deploy/kidsstream-status.service). Representa uma ferramenta interna
 * esquecida exposta à rede — um achado clássico de varredura de portas que vai além da simples
 * existência da porta aberta: a rota /debug expõe informações internas fictícias e uma flag.
 *
 * Executado por um usuário de sistema dedicado e sem privilégios (ksstatus), sem relação com
 * SSH, banco de dados ou a aplicação principal. Todos os dados aqui são fictícios.
 *
 * Gabarito completo: docs/private/vuln-03-exposed-service.md (fora do repositório público).
 */

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
header('Content-Type: text/plain; charset=utf-8');

if ($path === '/') {
    echo "Kids Stream - Internal Status\nOK\n";
    exit;
}

if ($path === '/debug') {
    echo "Kids Stream -- Painel interno (DEBUG) -- SEM AUTENTICACAO\n";
    echo "Ambiente: laboratorio\n";
    echo 'Host: ' . gethostname() . "\n";
    echo "Servicos internos conhecidos (ficticio, nao existem de fato):\n";
    echo " - kidsstream-billing.internal:9091\n";
    echo " - kidsstream-cache.internal:6379\n";
    echo "Equipe de operacoes (ficticia, nao e um e-mail real):\n";
    echo " - oncall.lab@kidsstream.lab\n";
    echo "Build: kidsstream-status dev-build (sem autenticacao -- achado do laboratorio)\n";
    echo "Flag do laboratorio: KIDSSTREAM{exposed_internal_status_panel}\n";
    exit;
}

http_response_code(404);
echo "not found\n";
