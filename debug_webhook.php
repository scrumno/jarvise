<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$token = $_ENV['TG_BOT_TOKEN'];
// Получаем инфо о вебхуке
$info = file_get_contents("https://api.telegram.org/bot{$token}/getWebhookInfo");
print_r(json_decode($info, true));
