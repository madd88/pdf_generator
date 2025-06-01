<?php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

return [
    'db' => [
        'host' => $_ENV['DB_HOST'],
        'name' => $_ENV['DB_NAME'],
        'user' => $_ENV['DB_USER'],
        'pass' => $_ENV['DB_PASS'],
    ],
    'redis' => [
        'host' => $_ENV['REDIS_HOST'],
        'port' => $_ENV['REDIS_PORT'],
    ],
    'app' => [
        'use_queue' => filter_var($_ENV['USE_QUEUE'], FILTER_VALIDATE_BOOL),
        'base_url' => $_ENV['BASE_URL'],
        'log_path' => $_ENV['LOG_PATH'],
        'pdf_storage' => realpath(__DIR__ . '/../' . $_ENV['PDF_STORAGE_PATH']),
        'pdf_storage_relative' => $_ENV['PDF_STORAGE_RELATIVE'], // Относительный путь
        'templates_path' => realpath(__DIR__ . '/../' . $_ENV['TEMPLATES_PATH']),
        'assets_path' => realpath(__DIR__ . '/../' . $_ENV['ASSETS_PATH']),
    ]
];