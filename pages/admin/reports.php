<?php
declare(strict_types=1);

Auth::require('report.read');
View::render('admin/reports', [
    'byGenre' => Db::all('SELECT g.name, COUNT(m.id) n FROM genres g LEFT JOIN movies m ON m.genre_id=g.id GROUP BY g.id, g.name ORDER BY n DESC'),
    'top' => Db::all(
        'SELECT m.title, ROUND(AVG(r.stars),2) avg, COUNT(*) n FROM ratings r JOIN movies m ON m.id=r.movie_id
         GROUP BY m.id, m.title ORDER BY avg DESC, n DESC LIMIT 10'
    ),
    'favs' => Db::all(
        'SELECT m.title, COUNT(*) n FROM favorites f JOIN movies m ON m.id=f.movie_id GROUP BY m.id, m.title ORDER BY n DESC LIMIT 10'
    ),
    'byRole' => Db::all('SELECT role, COUNT(*) n FROM users GROUP BY role'),
], 'Relatórios');
