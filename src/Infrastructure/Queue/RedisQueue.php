<?php
namespace App\Infrastructure\Queue;

use Predis\Client;
use App\Domain\Model\GeneratedFile;

class RedisQueue
{
    private $client;
    private $logger;
    private $enabled;

    public function __construct(
        string $host,
        int $port,
        $logger,
        bool $enabled
    ) {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host'   => $host,
            'port'   => $port,
        ]);
        $this->logger = $logger;
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function push(array $task): void
    {
        if (!$this->enabled) {
            throw new \RuntimeException("Queue is disabled");
        }
        $this->client->rpush('pdf_queue', [json_encode($task)]);
        $this->logger->info("Task pushed to queue: " . json_encode($task));
    }

    public function pop(): ?array
    {
        $task = $this->client->lpop('pdf_queue');
        return $task ? json_decode($task, true) : null;
    }
}