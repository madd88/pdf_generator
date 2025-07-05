<?php
namespace App\Application\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use App\Domain\Repository\InvoiceRepository;

class InvoiceController
{
    private InvoiceRepository $invoiceRepository;

    private string $baseUrl;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        string $baseUrl
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->baseUrl = $baseUrl;
    }

    public function getInvoice(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'] ?? null;

        if (!$id) {
            return $this->jsonResponse($response, ['error' => 'ID parameter is required'], 400);
        }
        $eq = explode("-", $id);

        $invoice = $this->invoiceRepository->findByExtIdAndYear((int)$eq[1], (int)$eq[0]);

        if (!$invoice) {
            return $this->jsonResponse($response, ['error' => 'Invoice not found'], 404);
        }

        return $this->jsonResponse($response, [
            'id' => $id,
            'file_url' => $this->baseUrl . "/files/" . $invoice['file_id'],
            'data' => json_decode($invoice['data'], true)
        ]);
    }

    private function jsonResponse(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
    {
        $response = $response->withStatus($status);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}