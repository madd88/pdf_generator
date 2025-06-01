<?php
use App\Infrastructure\Database\MysqlConnection;
use App\Infrastructure\Logger\FileLogger;
use App\Infrastructure\Queue\RedisQueue;
use App\Domain\Repository\FileRepository;
use App\Application\Service\PdfGenerator;
use App\Application\Service\QueueManager;
use App\Application\Controller\GenerateController;
use App\Application\Controller\FileController;
use Psr\Container\ContainerInterface;

$config = require __DIR__ . '/config.php';

return [
    // Конфигурация базы данных
    'db' => function() use ($config) {
        return new MysqlConnection(
            $config['db']['host'],
            $config['db']['name'],
            $config['db']['user'],
            $config['db']['pass']
        );
    },

    // Логгер
    'logger' => function() use ($config) {
        return new FileLogger($config['app']['log_path']);
    },

    // Очередь Redis
    'queue' => function(ContainerInterface $c) use ($config) {
        return new RedisQueue(
            $config['redis']['host'],
            $config['redis']['port'],
            $c->get('logger'),
            $config['app']['use_queue']
        );
    },

    // Репозиторий файлов
    FileRepository::class => function(ContainerInterface $c) {
        return new FileRepository($c->get('db'));
    },

    // Генератор PDF
    PdfGenerator::class => function(ContainerInterface $c) use ($config) {
        return new PdfGenerator(
            $config['app']['templates_path'],
            $config['app']['pdf_storage'],
            $config['app']['assets_path'], // Передаем путь к ассетам
            $c->get('logger')
        );
    },

    // Менеджер очередей
    QueueManager::class => function(ContainerInterface $c) {
        return new QueueManager(
            $c->get('queue'),
            $c->get(PdfGenerator::class),
            $c->get(FileRepository::class),
            $c->get('logger')
        );
    },

    // Контроллер генерации
    GenerateController::class => function(ContainerInterface $c) use ($config) {
        return new GenerateController(
            $c->get(QueueManager::class),
            $config['app']['base_url'],
            $config['app']['pdf_storage_relative']
        );
    },

    // Контроллер файлов
    FileController::class => function(ContainerInterface $c) use ($config) {
        return new FileController(
            $c->get(FileRepository::class),
            $config['app']['base_url'],
            $config['app']['pdf_storage_relative'] // Добавим новый параметр
        );
    },
];