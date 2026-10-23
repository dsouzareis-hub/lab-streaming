<?php
declare(strict_types=1);

/** Upload seguro de foto de perfil: valida conteúdo real, re-codifica com GD e gera nome no servidor. */
final class Avatar
{
    private const MAX_BYTES = 2 * 1024 * 1024;
    private const MAX_DIM = 4000;

    /** @return array{0:?string,1:?string} [nomeDoArquivo, erro] */
    public static function store(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return [null, 'Falha no envio do arquivo.'];
        }
        if ($file['size'] > self::MAX_BYTES) {
            return [null, 'A imagem deve ter no máximo 2 MB.'];
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $info = @getimagesize($file['tmp_name']);
        if (!$info || !in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true) || $info['mime'] !== $mime) {
            return [null, 'Formato inválido. Envie JPEG, PNG ou WebP.'];
        }
        if ($info[0] > self::MAX_DIM || $info[1] > self::MAX_DIM || $info[0] < 1 || $info[1] < 1) {
            return [null, 'Dimensões da imagem inválidas.'];
        }
        $src = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($file['tmp_name']),
            'image/png' => @imagecreatefrompng($file['tmp_name']),
            default => @imagecreatefromwebp($file['tmp_name']),
        };
        if (!$src) {
            return [null, 'Não foi possível processar a imagem.'];
        }
        // Recorte quadrado 256x256, o que descarta metadados e qualquer carga embutida.
        $side = min($info[0], $info[1]);
        $dst = imagecreatetruecolor(256, 256);
        imagecopyresampled($dst, $src, 0, 0, intdiv($info[0] - $side, 2), intdiv($info[1] - $side, 2), 256, 256, $side, $side);
        $name = bin2hex(random_bytes(16)) . '.jpg';
        $ok = imagejpeg($dst, BASE_PATH . '/public/uploads/' . $name, 85);
        imagedestroy($src);
        imagedestroy($dst);
        return $ok ? [$name, null] : [null, 'Não foi possível salvar a imagem.'];
    }

    public static function delete(?string $name): void
    {
        if ($name && preg_match('/^[a-f0-9]{32}\.jpg$/', $name)) {
            @unlink(BASE_PATH . '/public/uploads/' . $name);
        }
    }
}
