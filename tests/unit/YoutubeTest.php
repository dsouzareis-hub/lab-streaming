<?php
declare(strict_types=1);

test('Youtube::extractId aceita youtube.com/watch?v=', function () {
    assertSame('dQw4w9WgXcQ', Youtube::extractId('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
});

test('Youtube::extractId aceita youtu.be/', function () {
    assertSame('dQw4w9WgXcQ', Youtube::extractId('https://youtu.be/dQw4w9WgXcQ'));
});

test('Youtube::extractId aceita youtube.com/embed/', function () {
    assertSame('dQw4w9WgXcQ', Youtube::extractId('https://www.youtube.com/embed/dQw4w9WgXcQ'));
});

test('Youtube::extractId aceita youtube.com/shorts/', function () {
    assertSame('dQw4w9WgXcQ', Youtube::extractId('https://www.youtube.com/shorts/dQw4w9WgXcQ'));
});

test('Youtube::extractId rejeita host arbitrário (anti-SSRF de origem)', function () {
    assertSame(null, Youtube::extractId('https://evil.example.com/watch?v=dQw4w9WgXcQ'));
});

test('Youtube::extractId rejeita esquema não-http(s)', function () {
    assertSame(null, Youtube::extractId('javascript:alert(1)'));
});

test('Youtube::extractId rejeita URL sem vídeo', function () {
    assertSame(null, Youtube::extractId('https://www.youtube.com/'));
});

test('Youtube::embedUrl usa domínio youtube-nocookie.com', function () {
    assertTrue(str_starts_with(Youtube::embedUrl('abc12345678'), 'https://www.youtube-nocookie.com/embed/'), 'embed deve usar youtube-nocookie.com');
});
