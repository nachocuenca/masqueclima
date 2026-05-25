<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$contentDir = $root . '/content';

function ensure_dir(string $dir): void {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

function write_json(string $path, mixed $data): void {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        throw new RuntimeException('Could not encode JSON for ' . $path);
    }
    file_put_contents($path, $json . PHP_EOL);
}

ensure_dir($contentDir . '/legacy');
ensure_dir($contentDir . '/i18n');

$config = require $root . '/app/config.php';
write_json($contentDir . '/legacy/config.json', $config);

$langs = $config['brand']['langs'] ?? ['es', 'en', 'de', 'nl', 'ru'];
foreach ($langs as $lang) {
    $translationFile = $root . "/app/translations/{$lang}.php";
    if (is_file($translationFile)) {
        write_json($contentDir . "/i18n/{$lang}.json", require $translationFile);
    }

    foreach (['services', 'areas', 'guides', 'cases'] as $type) {
        $source = $root . "/app/content/{$type}/{$lang}.php";
        if (is_file($source)) {
            write_json($contentDir . "/legacy/{$type}.{$lang}.json", require $source);
        }
    }
}

echo "Legacy content exported to content/.\n";
