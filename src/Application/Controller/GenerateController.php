<?php
namespace App\Application\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Application\Service\QueueManager;
use Slim\Psr7\Response;

class GenerateController
{
    private $queueManager;
    private $baseUrl;
    private $pdfStorageRelative;

    public function __construct(
        QueueManager $queueManager,
        string $baseUrl,
        string $pdfStorageRelative
    ) {
        $this->queueManager = $queueManager;
        $this->baseUrl = $baseUrl;
        $this->pdfStorageRelative = $pdfStorageRelative;
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);

        if (empty($data['template'])) {
            return $this->jsonResponse($response, ['error' => 'Template name required'], 400);
        }

        try {
            $fileId = $this->queueManager->processRequest($data);

            // Формируем полные URL
            $statusUrl = $this->getFullUrl("/files/{$fileId}");
            $fileUrl = $this->getFullUrl("/{$this->pdfStorageRelative}/" . $this->queueManager->getFilename($fileId));

            return $this->jsonResponse($response, [
                'id' => $fileId,
                'status_url' => $statusUrl,
                'file_url' => $fileUrl
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, ['error' => $e->getMessage()], 400);
        }
    }

    private function getFullUrl(string $path): string
    {
        $base = rtrim($this->baseUrl, '/');
        $path = ltrim($path, '/');
        return "{$base}/{$path}";
    }

    private function jsonResponse(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
    {
        $response = $response->withStatus($status);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}