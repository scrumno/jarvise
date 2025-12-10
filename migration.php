<?php

// migrate.php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Dotenv\Dotenv;

// Загружаем .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Настраиваем Eloquent (копия логики из settings.php, чтобы не зависеть от Slim тут)
$capsule = new Capsule();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['DB_HOST'] ?? 'localhost',
    'database'  => $_ENV['DB_DATABASE'] ?? 'jarvis_db',
    'username'  => $_ENV['DB_USERNAME'] ?? 'root',
    'password'  => $_ENV['DB_PASSWORD'] ?? '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Создание таблицы
try {
    if (!Capsule::schema()->hasTable('chat_messages')) {
        Capsule::schema()->create('chat_messages', function (Blueprint $table) {
            $table->id();
            // chat_id делаем строкой, так как это может быть Telegram ID (число) или хеш сессии
            $table->string('chat_id')->index();
            $table->enum('role', ['user', 'model']);
            $table->text('content');
            $table->timestamps();
        });
        print "✅ Таблица 'chat_messages' успешно создана.\n";
    } else {
        print "ℹ️ Таблица уже существует.\n";
    }
} catch (\Exception $e) {
    print '❌ Ошибка: ' . $e->getMessage() . "\n";
}
