<?php
namespace App\Application\Service;

use App\Domain\Model\GeneratedFile;
use App\Domain\Repository\FileRepository;
use App\Infrastructure\Queue\RedisQueue;

class QueueManager
{
    private $queue;
    private $pdfGenerator;
    private $fileRepository;
    private $logger;
    private $useQueue;

    public function __construct(
        RedisQueue $queue,
        PdfGenerator $pdfGenerator,
        FileRepository $fileRepository,
        $logger
    ) {
        $this->queue = $queue;
        $this->pdfGenerator = $pdfGenerator;
        $this->fileRepository = $fileRepository;
        $this->logger = $logger;
        $this->useQueue = $queue->isEnabled();
    }

    public function processRequest(array $data): string
    {
        $template = $data['template'];
        unset($data['template']);

        if ($this->useQueue) {
            $fileId = uniqid();
            $this->queue->push([
                'template' => $template,
                'data' => $data,
                'fileId' => $fileId
            ]);
            $this->logger->info("Task queued for template $template with ID $fileId");
            return $fileId;
        } else {
            $file = $this->pdfGenerator->generate($template, $data);
            $this->fileRepository->save($file);
            return $file->getId();
        }
    }

    public function processTask(array $task): void
    {
        try {
            $file = $this->pdfGenerator->generate($task['template'], $task['data']);
            
            // Устанавливаем заданный ID
            $reflection = new \ReflectionClass($file);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($file, $task['fileId']);
            
            $this->fileRepository->save($file);
            $this->logger->info("PDF generated for ID: " . $file->getId());
        } catch (\Exception $e) {
            $this->logger->error("Error processing task: " . $e->getMessage());
        }
    }

    public function getFilename(string $fileId): string
    {
        return $fileId . '.pdf';
    }

}