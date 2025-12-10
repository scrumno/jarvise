<?php

// 1. Подключаем автозагрузчик (выходим на 6 уровней вверх в корень)
require __DIR__ . '/../../../../../../vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use Dotenv\Dotenv;

// 2. ЗАГРУЖАЕМ .ENV ФАЙЛ
// Указываем путь к папке, где лежит .env (корень проекта)
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../../../');
try {
    $dotenv->load();
} catch (\Exception $e) {
    die("Ошибка: Не найден файл .env в корне проекта или нет прав на чтение.\n");
}

// Проверяем, загрузились ли данные (для отладки)
if (empty($_ENV['TG_APP_ID']) || empty($_ENV['TG_APP_HASH'])) {
    die("Ошибка: Переменные TG_APP_ID или TG_APP_HASH пустые. Проверьте .env файл.\n");
}

// 3. Настройки MadelineProto
$settings = new Settings;
$settings->getAppInfo()->setApiId((int)$_ENV['TG_APP_ID']); // Приводим к int
$settings->getAppInfo()->setApiHash($_ENV['TG_APP_HASH']);

// Путь к файлу сессии (положим его в корень, чтобы потом не искать)
$sessionFile = __DIR__ . '/../../../../../../session.madeline';

echo "Запускаем авторизацию...\n";
echo "Файл сессии будет сохранен здесь: $sessionFile\n";

$MadelineProto = new API($sessionFile, $settings);
$MadelineProto->start();

echo "Успех! Сессия сохранена.\n";
