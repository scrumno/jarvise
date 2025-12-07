<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use App\Controllers\WeatherController;

require __DIR__ . '/../vendor/autoload.php';

// 1. Создаем контейнер
$containerBuilder = new ContainerBuilder();
$definitions = require __DIR__ . '/../config/container.php';
$definitions($containerBuilder);
$container = $containerBuilder->build();

// 2. Инициализируем БД (глобально)
try {
    $container->get('db');
} catch (\Exception $e) {
    // Если БД упала, приложение должно работать, но без базы
    // error_log($e->getMessage());
}

// 3. Создаем App
AppFactory::setContainer($container);
$app = AppFactory::create();

// Middleware ошибок (показывать детали)
$app->addErrorMiddleware(true, true, true);

// --- РОУТЫ ---

$app->get('/', function ($request, $response) {
    $response->getBody()->write('Jarvis System Online. VDS Active.');

    return $response;
});

// Роут для крона или кнопки в PWA
$app->get('/api/check-weather', [WeatherController::class, 'check']);

$app->run();
