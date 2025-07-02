<?php
namespace App\Domain\Repository;

use App\Infrastructure\Database\MysqlConnection;

class DiseasesRepository
{
    private MysqlConnection $connection;

    public function __construct(MysqlConnection $connection)
    {
        $this->connection = $connection;
    }

    public function save(GeneratedFile $file): void
    {
    }

    public function findByNumber(string $number): ?array
    {
        $sql = "SELECT * FROM diseases WHERE batch_number = :batch_number";
        $stmt = $this->connection->execute($sql, [':batch_number' => $number]);
        $data = $stmt->fetch(2);

        if (!$data) {
            return null;
        }

        return $data;
    }
}