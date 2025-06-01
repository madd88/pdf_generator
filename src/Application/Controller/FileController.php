<?php
namespace App\Application\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Domain\Repository\FileRepository;
use Slim\Psr7\Response;

class FileController
{
    private $fileRepository;
    private $baseUrl;
    private $pdfStoragePath;

    public function __construct(
        FileRepository $fileRepository,
        string $baseUrl,
        string $pdfStoragePath
    ) {
        $this->fileRepository = $fileRepository;
        $this->baseUrl = $baseUrl;
        $this->pdfStoragePath = $pdfStoragePath;
    }

    public function getFile(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'] ?? null;

        if (!$id) {
            return $this->jsonResponse($response, ['error' => 'ID parameter is required'], 400);
        }

        $file = $this->fileRepository->find($id);

        if (!$file) {
            return $this->jsonResponse($response, ['error' => 'File not found'], 404);
        }

        // Формируем полный URL к файлу
        $fileUrl = $this->getFullFileUrl($file->getFilename());

        return $this->jsonResponse($response, [
            'id' => $file->getId(),
            'url' => $fileUrl
        ]);
    }

    private function getFullFileUrl(string $filename): string
    {
        // Убираем дублирующиеся слеши
        $base = rtrim($this->baseUrl, '/');
        $path = ltrim($this->pdfStoragePath, '/');

        return sprintf('%s/%s/%s', $base, $path, $filename);
    }

    private function jsonResponse(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
    {
        $response = $response->withStatus($status);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}