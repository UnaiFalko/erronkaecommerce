<?php
// Solo se pueden pedir los logotipos incluidos en esta lista.
$logos = [
    'pokemon-go' => 'pokemon-go.svg',
    'valorant' => 'valorant.svg',
    'fortnite' => 'fortnite.svg',
    'ea-sports-fc' => 'ea-sports-fc.svg',
    'netflix' => 'netflix.svg',
    'prime-video' => 'prime-video.svg',
    'disney-plus' => 'disney-plus.svg',
    'hbo' => 'hbo.svg',
    'crunchyroll' => 'crunchyroll.svg',
    'spotify' => 'spotify.svg',
    'youtube-premium' => 'youtube-premium.svg',
];

$marca = $_GET['marca'] ?? '';
if (!isset($logos[$marca])) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Logo no encontrado.';
    exit;
}

// Comprueba que el archivo exista antes de enviarlo al navegador.
$ruta = __DIR__ . DIRECTORY_SEPARATOR . $logos[$marca];
if (!is_file($ruta) || !is_readable($ruta)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'No se pudo cargar el logo.';
    exit;
}

header('Content-Type: image/svg+xml; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=86400');
readfile($ruta);
