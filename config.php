<?php
// Конфигурация приложения
define('CACHE_DIR', __DIR__ . '/cache/');
define('CACHE_TIME', 300); // 5 минут в секундах

// Сайты по умолчанию для проверки
$defaultSites = [
    'google' => 'https://www.google.com',
    'youtube' => 'https://www.youtube.com',
    'facebook' => 'https://www.facebook.com',
    'twitter' => 'https://twitter.com',
    'instagram' => 'https://www.instagram.com',
    'linkedin' => 'https://www.linkedin.com',
    'github' => 'https://github.com',
    'stackoverflow' => 'https://stackoverflow.com',
    'wikipedia' => 'https://www.wikipedia.org',
    'amazon' => 'https://www.amazon.com'
];

// Проверяем существование папки cache
if (!file_exists(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, true);
}

// Разрешенные CORS (для AJAX запросов)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
?>