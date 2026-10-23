<?php
declare(strict_types=1);

final class Youtube
{
    /** Extrai o ID de um vídeo apenas de hosts YouTube conhecidos. */
    public static function extractId(string $url): ?string
    {
        $p = parse_url(trim($url));
        if (!$p || !isset($p['host']) || !in_array($p['scheme'] ?? '', ['http', 'https'], true)) {
            return null;
        }
        $host = strtolower($p['host']);
        $id = null;
        if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
            $path = $p['path'] ?? '';
            if ($path === '/watch') {
                parse_str($p['query'] ?? '', $q);
                $id = is_string($q['v'] ?? null) ? $q['v'] : null;
            } elseif (preg_match('#^/(embed|shorts)/([^/?]+)$#', $path, $m)) {
                $id = $m[2];
            }
        } elseif ($host === 'youtu.be') {
            $id = ltrim($p['path'] ?? '', '/');
        }
        return ($id !== null && preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) ? $id : null;
    }

    /** Consulta o oEmbed oficial (host fixo, ID validado). true/false, ou null se não foi possível verificar. */
    public static function available(string $id): ?bool
    {
        if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) {
            return false;
        }
        $url = 'https://www.youtube.com/oembed?format=json&url=' . rawurlencode('https://www.youtube.com/watch?v=' . $id);
        $ctx = stream_context_create(['http' => ['timeout' => 4, 'ignore_errors' => true, 'follow_location' => 0]]);
        $body = @file_get_contents($url, false, $ctx);
        if ($body === false || empty($http_response_header[0])) {
            return null;
        }
        if (preg_match('#\s(200)\s#', $http_response_header[0])) {
            return true;
        }
        return preg_match('#\s(401|403|404)\s#', $http_response_header[0]) ? false : null;
    }

    public static function embedUrl(string $id): string
    {
        return 'https://www.youtube-nocookie.com/embed/' . rawurlencode($id) . '?rel=0';
    }

    public static function thumb(string $id): string
    {
        return 'https://i.ytimg.com/vi/' . rawurlencode($id) . '/hqdefault.jpg';
    }
}
