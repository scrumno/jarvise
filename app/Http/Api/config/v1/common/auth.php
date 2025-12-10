<?php

require 'vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;

$settings = new Settings();
$settings->getAppInfo()->setApiId($_ENV['TG_APP_ID']);
$settings->getAppInfo()->setApiHash($_ENV['TG_APP_HASH']);

$MadelineProto = new API('session.madeline', $settings);

$MadelineProto->start();

print 'Авторизация прошла успешно! Файл session.madeline создан.';
