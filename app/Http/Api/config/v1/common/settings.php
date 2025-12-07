<?php

use Dotenv\Dotenv;

// Загружаем .env, если еще не загружен
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../../../'); // Путь до корня
$dotenv->safeLoad();

return [
    'settings' => [
        'displayErrorDetails' => true, // В продакшене false
        'logError'            => true,
        'logErrorDetails'     => true,

        'db' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['DB_HOST'] ?? 'localhost',
            'database'  => $_ENV['DB_DATABASE'] ?? 'jarvis_db',
            'username'  => $_ENV['DB_USERNAME'] ?? 'root',
            'password'  => $_ENV['DB_PASSWORD'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ],
        'api' => [
            'gemini_key' => $_ENV['GEMINI_API_KEY'] ?? '',
            'proxy_url'  => $_ENV['CLOUDFLARE_PROXY'] ?? '',
            'tg_token'   => $_ENV['TG_BOT_TOKEN'] ?? '',
            'tg_chat_id' => $_ENV['TG_CHAT_ID'] ?? '',
        ],
    ],
];
