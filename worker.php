#!/usr/bin/env php
<?php
use App\Application\Service\QueueManager;

require __DIR__ . '/vendor/autoload.php';

$container = require __DIR__ . '/config/dependencies.php';
$queueManager = $container->get(QueueManager::class);
$queue = $container->get('queue');

if (!$queue->isEnabled()) {
    echo "Queue is disabled. Exiting.\n";
    exit(1);
}

echo "Worker started. Waiting for tasks...\n";

while (true) {
    $task = $queue->pop();
    if ($task) {
        echo "Processing task: " . json_encode($task) . "\n";
        $queueManager->processTask($task);
    } else {
        sleep(1);
    }
}