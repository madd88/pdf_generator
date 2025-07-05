<?php

namespace App\Domain\Repository;

use App\Domain\Model\Invoice;
use App\Infrastructure\Database\MysqlConnection;

class InvoiceRepository
{
    private MysqlConnection $connection;

    public function __construct(MysqlConnection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Invoice $invoice): void
    {
        $sql = "INSERT INTO invoices 
                (ext_id, data, year, file_id) 
                VALUES (:ext_id, :data, :year, :file_id)";

        $params = [
            ':ext_id'  => $invoice->getExtId(),
            ':data'    => json_encode($invoice->getData()),
            ':year'    => $invoice->getYear(),
            ':file_id' => $invoice->getFileId()
        ];

        $this->connection->prepare($sql)->execute($params);
    }

    public function findByExtId(int $id): ?array
    {
        $sql = "SELECT * FROM invoices WHERE ext_id = :id";
        $stmt = $this->connection->execute($sql, [':id' => $id]);
        $data = $stmt->fetch(2);

        if (!$data) {
            return null;
        }

        return $data;
    }

    public function findByExtIdAndYear(int $id, int $year): ?array
    {
        $sql = "SELECT data, file_id FROM invoices WHERE ext_id = :id and year = :year";
        $stmt = $this->connection->execute($sql, [':id' => $id, ':year' => $year]);
        $data = $stmt->fetch(2);

        if (!$data) {
            return null;
        }

        return $data;
    }

    function generateExtId(?int $year = null): string
    {
        $currentYear = $year ?? date('Y');
        $maxAttempts = 9999; // Максимальное количество попыток
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            // Генерируем 4 случайные цифры
            $randomDigits = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $id = $randomDigits;

            // Проверяем уникальность в БД
            $stmt = $this->connection->prepare("SELECT COUNT(*) FROM invoices WHERE ext_id = :id");
            $stmt->execute([':id' => $id]);
            $exists = $stmt->fetchColumn();

            if (!$exists) {
                return $id;
            }

            $attempt++;
        }

        throw new \RuntimeException("No unique id found after {$maxAttempts} attempts");
    }
}