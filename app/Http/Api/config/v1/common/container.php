<?php

use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder();

// Подключаем все файлы из common
$definitions = array_merge(
    require __DIR__ . '/common/settings.php',
    require __DIR__ . '/common/dependencies.php'
);

$containerBuilder->addDefinitions($definitions);

return $containerBuilder->build();
