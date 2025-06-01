<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Загружаем контейнер зависимостей
$dependencies = require __DIR__ . '/../config/dependencies.php';

// Создаем контейнер DI
$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions($dependencies);
$container = $containerBuilder->build();

// Устанавливаем контейнер в AppFactory
AppFactory::setContainer($container);
$app = AppFactory::create();

// Регистрация маршрутов
$app->post('/generate', [\App\Application\Controller\GenerateController::class, 'handle']);
$app->get('/files/{id}', [\App\Application\Controller\FileController::class, 'getFile']);

// Middleware
$app->addBodyParsingMiddleware(); // Для обработки JSON
$app->addErrorMiddleware(true, true, true);

// Проверка существования контроллеров
if (!class_exists(\App\Application\Controller\FileController::class)) {
    die("FileController class not found");
}

if (!method_exists(\App\Application\Controller\FileController::class, 'getFile')) {
    die("getFile method not found in FileController");
}

$app->run();