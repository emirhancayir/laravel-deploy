<?php
// Basit resim sunucu - route bypass

$path = isset($_GET['p']) ? $_GET['p'] : '';

if (empty($path)) {
    die('Kullanım: serve-image.php?p=urunler/dosya.jpg');
}

// Güvenlik - path traversal engelle
$path = str_replace(['..', '\\'], '', $path);

$fullPath = dirname(__DIR__) . '/storage/app/public/' . $path;

if (!file_exists($fullPath)) {
    // Statik placeholder resim
    $placeholder = __DIR__ . '/images/no-image.png';
    if (file_exists($placeholder)) {
        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=31536000');
        readfile($placeholder);
        exit;
    }
    // Fallback - basit SVG
    header('Content-Type: image/svg+xml');
    header('Cache-Control: public, max-age=31536000');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
        <rect width="300" height="300" fill="#f0f0f0"/>
        <text x="150" y="150" text-anchor="middle" fill="#999" font-family="Arial" font-size="20">Resim Yok</text>
    </svg>';
    exit;
}

$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
];

$ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
$mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: public, max-age=31536000');

readfile($fullPath);
