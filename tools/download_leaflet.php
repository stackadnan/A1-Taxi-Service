<?php
// Simple helper to download leaflet and leaflet.draw assets into public/vendor/leaflet
$dest = __DIR__ . '/../public/vendor/leaflet';
if (!is_dir($dest)) mkdir($dest, 0755, true);
$files = [
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js' => $dest . '/leaflet.js',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' => $dest . '/leaflet.css',
    'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js' => $dest . '/leaflet.draw.js',
    'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css' => $dest . '/leaflet.draw.css',
];
foreach ($files as $url => $path) {
    echo "Downloading $url...\n";
    $content = @file_get_contents($url);
    if ($content === false) {
        echo "Failed to download $url\n";
        continue;
    }
    file_put_contents($path, $content);
    echo "Saved to $path\n";
}
echo "Done.\n";