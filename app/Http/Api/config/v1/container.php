<?php

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use GuzzleHttp\Client;
use Dotenv\Dotenv;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'settings' => function () {
            if (file_exists(__DIR__ . '/../.env')) {
                $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
                $dotenv->load();
            }

            return [
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
            ];
        },

        'db' => function (ContainerInterface $c) {
            $settings = $c->get('settings')['db'];
            $capsule = new Capsule();
            $capsule->addConnection($settings);
            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        },

        Client::class => function (ContainerInterface $c) {
            return new Client([
                'timeout' => 10.0,
                'verify'  => false, //ssl
            ]);
        },
    ]);
};
