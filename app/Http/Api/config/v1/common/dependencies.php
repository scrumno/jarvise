<?php

use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use GuzzleHttp\Client;

return [
    // 1. Настройка Eloquent
    'db' => function (ContainerInterface $c) {
        $settings = $c->get('settings')['db'];
        $capsule = new Capsule();
        $capsule->addConnection($settings);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    },

    // 2. HTTP Клиент (Guzzle)
    Client::class => function (ContainerInterface $c) {
        return new Client([
            'timeout' => 10.0,
            'verify'  => false,
        ]);
    },

    // Интерфейс контейнера для инъекции в сервисы (если нужно)
    ContainerInterface::class => function (ContainerInterface $c) {
        return $c;
    },
];
