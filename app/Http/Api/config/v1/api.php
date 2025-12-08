<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\WeatherController;
use App\Http\Action\Telegram\CreatePostTelegramAction\Action;

return function ($app) {
    $app->group('/api/v1', function (RouteCollectorProxy $group) {
        $group->get('/weather-check', [WeatherController::class, 'handle']);

        $group->group('/telegram', function (RouteCollectorProxy $subgroup) {
            $subgroup->get('/post', Action::class);
        });
    });
};
