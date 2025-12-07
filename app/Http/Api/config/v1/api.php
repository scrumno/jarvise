<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\WeatherController;

return function ($app) {
    $app->group('/api/v1', function (RouteCollectorProxy $group) {
        $group->get('/weather-check', [WeatherController::class, 'handle']);
    });
};
