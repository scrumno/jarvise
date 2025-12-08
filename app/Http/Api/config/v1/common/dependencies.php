<?php

use App\AI\Gemini\Service\GeminiService;
use App\Telegram\Service\TelegramService;
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

    // TelegramService
    TelegramService::class => function (ContainerInterface $c) {
        $cfg = $c->get('settings')['api'];
        $http = $c->get(Client::class);
        $chatId = $cfg['tg_chat_id'];

        $url = "https://api.telegram.org/bot{$cfg['tg_token']}/sendMessage";

        return new TelegramService(
            url: $url,
            chatId: $chatId,
            http: $http
        );
    },

    GeminiService::class => function (ContainerInterface $c) {
        $cfg = $c->get('settings')['api'];
        $proxy = rtrim($cfg['proxy_url'], '/');
        $key = $cfg['gemini_key'];
        $http = $c->get(Client::class);

        // Используем gemini-2.5-flash для стабильности
        $url = "{$proxy}/v1beta/models/gemini-2.5-flash:generateContent?key={$key}";

        return new GeminiService(
            url: $url,
            http: $http,
            c: $c,
        );
    },
];
