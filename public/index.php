<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

file_put_contents(
    __DIR__ . '/../entry_debug.log',
    date('H:i:s') . ' Request: ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . "\n",
    FILE_APPEND
);

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// 1. Получаем контейнер из конфига
$container = require __DIR__ . '/../app/Http/Api/config/v1/container.php';

// 2. Инициализируем Eloquent (БД)
$container->get('db');

// 3. Создаем приложение
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->setBasePath(''); // Если проект в корне домена

// 4. Подключаем роуты
$routes = require __DIR__ . '/../app/Http/Api/config/v1/api.php';
$routes($app);

// 5. Middleware
$app->addErrorMiddleware(true, true, true);

$app->run();
