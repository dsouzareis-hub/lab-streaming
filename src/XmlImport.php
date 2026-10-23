<?php
declare(strict_types=1);

/**
 * Vulnerabilidade intencional 1 — XXE (XML External Entity).
 *
 * Importador de metadados de filme em XML (colaborador/administrador). O parser habilita
 * DTD e expansão de entidades (LIBXML_NOENT | LIBXML_DTDLOAD), o comportamento inseguro
 * clássico de XXE: um DOCTYPE com uma entidade externa é expandido e seu conteúdo é refletido
 * nos campos da pré-visualização.
 *
 * Contenção de laboratório: um resolvedor de entidades customizado restringe qualquer leitura
 * a arquivos dentro de SANDBOX e bloqueia esquemas de rede (http/ftp/...), para que a falha seja
 * demonstrável sem permitir leitura arbitrária do sistema ou requisições a destinos externos.
 *
 * Gabarito completo: docs/private/vuln-01-xxe.md (fora do repositório público).
 */
final class XmlImport
{
    private const SANDBOX = BASE_PATH . '/storage/imports';

    private static function installLoader(): void
    {
        libxml_set_external_entity_loader(static function (?string $public, ?string $system) {
            if ($system === null) {
                return null;
            }
            $scheme = parse_url($system, PHP_URL_SCHEME);
            if ($scheme !== null && $scheme !== 'file') {
                return null; // bloqueia SSRF: nenhuma requisição http/ftp/php/data/etc.
            }
            $path = $scheme === 'file' ? (parse_url($system, PHP_URL_PATH) ?? '') : $system;
            $real = realpath($path);
            $sandbox = realpath(self::SANDBOX);
            if ($real === false || $sandbox === false || !str_starts_with($real, $sandbox . DIRECTORY_SEPARATOR)) {
                return null; // fora do sandbox do laboratório: recusado
            }
            return fopen($real, 'rb') ?: null;
        });
    }

    /**
     * Processamento INSEGURO (vulnerabilidade intencional): DTD e entidades habilitadas.
     * @return array{title:?string,synopsis:?string,youtube_url:?string,error:?string}
     */
    public static function parseVulnerable(string $xml): array
    {
        if (strlen($xml) > 200_000) {
            return ['title' => null, 'synopsis' => null, 'youtube_url' => null, 'error' => 'XML muito grande.'];
        }
        self::installLoader();
        $prev = libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $ok = @$doc->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD | LIBXML_DTDATTLIST);
        $err = libxml_get_last_error();
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        if (!$ok || !$doc->documentElement) {
            return ['title' => null, 'synopsis' => null, 'youtube_url' => null, 'error' => $err->message ?? 'XML inválido.'];
        }
        $text = static fn (string $tag): ?string => $doc->getElementsByTagName($tag)->item(0)?->textContent;
        return ['title' => $text('title'), 'synopsis' => $text('synopsis'), 'youtube_url' => $text('youtube_url'), 'error' => null];
    }
}
