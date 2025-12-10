<?php

// 1. Загружаем токен из .env (или вставьте вручную ниже)
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$token = $_ENV['TG_BOT_TOKEN'] ?? ''; // Или впишите токен в кавычках жестко
$domain = 'https://moontesearch.fvds.ru';
$route = '/api/v1/telegram/chat'; // Ваш роут для вебхука

// Путь к публичному ключу
$certPath = '/var/www/jarvis/public.pem';

if (!$token) {
    die("Ошибка: Токен не найден в .env\n");
}

if (!file_exists($certPath)) {
    die("Ошибка: Сертификат не найден по пути $certPath\n");
}

$url = "https://api.telegram.org/bot{$token}/setWebhook";

// Формируем POST запрос с файлом
$postFields = [
    'url' => $domain . $route,
    'certificate' => new CURLFile($certPath)
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);

echo "Ответ Telegram: " . $result . "\n";
