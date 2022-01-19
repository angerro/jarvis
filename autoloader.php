<?php

$config = include 'config.php';

spl_autoload_register(function ($class) {
    $pathParts = explode('\\', $class);
    $pathParts[0] = strtolower($pathParts[0]);
    $classPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathParts) . '.php';
    if (file_exists($classPath)) {
        require $classPath;
    } else {
        throw new \Exception("Ошибка загрузки '$classPath'");
    }
});

// Регистрируем команды
foreach ($config['commands'] as $command){
    spl_autoload_call($command);
}