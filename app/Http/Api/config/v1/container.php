<?php

use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder();

$settings = require __DIR__ . '/common/settings.php';
$dependencies = require __DIR__ . '/common/dependencies.php';

$containerBuilder->addDefinitions($settings);
$containerBuilder->addDefinitions($dependencies);

return $containerBuilder->build();
