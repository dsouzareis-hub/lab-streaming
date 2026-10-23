<?php
declare(strict_types=1);

final class Movie
{
    private const BASE = 'SELECT m.*, g.name AS genre_name, g.slug AS genre_slug,
        (SELECT ROUND(AVG(stars),1) FROM ratings r WHERE r.movie_id=m.id) AS avg_stars
        FROM movies m JOIN genres g ON g.id=m.genre_id';

    public static function latest(int $n = 12): array
    {
        return Db::all(self::BASE . ' WHERE m.hidden=0 ORDER BY m.created_at DESC, m.id DESC LIMIT ' . (int) $n);
    }

    public static function byGenre(int $genreId, int $limit = 60): array
    {
        return Db::all(self::BASE . ' WHERE m.hidden=0 AND m.genre_id=? ORDER BY m.title LIMIT ' . (int) $limit, [$genreId]);
    }

    public static function search(string $q, int $limit = 60): array
    {
        $like = '%' . addcslashes($q, '%_\\') . '%';
        return Db::all(
            self::BASE . ' WHERE m.hidden=0 AND (m.title LIKE ? OR m.synopsis LIKE ?) ORDER BY m.title LIMIT ' . (int) $limit,
            [$like, $like]
        );
    }

    public static function all(bool $includeHidden, int $page = 1, int $per = 24): array
    {
        $where = $includeHidden ? '' : ' WHERE m.hidden=0';
        $off = max(0, ($page - 1) * $per);
        return Db::all(self::BASE . $where . ' ORDER BY m.title LIMIT ' . (int) $per . ' OFFSET ' . (int) $off);
    }

    public static function count(bool $includeHidden): int
    {
        return (int) Db::one('SELECT COUNT(*) c FROM movies' . ($includeHidden ? '' : ' WHERE hidden=0'))['c'];
    }

    public static function find(int $id, bool $includeHidden = false): ?array
    {
        return Db::one(self::BASE . ' WHERE m.id=?' . ($includeHidden ? '' : ' AND m.hidden=0'), [$id]);
    }

    public static function validate(array $in): array
    {
        $errors = [];
        $title = trim((string) ($in['title'] ?? ''));
        $syn = trim((string) ($in['synopsis'] ?? ''));
        $genre = (int) ($in['genre_id'] ?? 0);
        $age = (string) ($in['age_rating'] ?? '');
        $year = trim((string) ($in['release_year'] ?? ''));
        $vid = Youtube::extractId((string) ($in['youtube_url'] ?? ''));

        if (mb_strlen($title) < 2 || mb_strlen($title) > 160) {
            $errors[] = 'Título deve ter entre 2 e 160 caracteres.';
        }
        if (mb_strlen($syn) < 10 || mb_strlen($syn) > 4000) {
            $errors[] = 'Sinopse deve ter entre 10 e 4000 caracteres.';
        }
        if (!Db::one('SELECT id FROM genres WHERE id=?', [$genre])) {
            $errors[] = 'Gênero inválido.';
        }
        if (!in_array($age, ['L', '10', '12'], true)) {
            $errors[] = 'Classificação indicativa inválida.';
        }
        if ($year !== '' && (!ctype_digit($year) || (int) $year < 1900 || (int) $year > (int) date('Y') + 2)) {
            $errors[] = 'Ano de lançamento inválido.';
        }
        if ($vid === null) {
            $errors[] = 'URL do YouTube inválida. Use links youtube.com/watch?v=, youtu.be/ ou youtube.com/embed/.';
        }
        return [$errors, [
            'title' => $title, 'synopsis' => $syn, 'genre_id' => $genre, 'age_rating' => $age,
            'release_year' => $year === '' ? null : (int) $year, 'youtube_id' => $vid,
        ]];
    }
}
