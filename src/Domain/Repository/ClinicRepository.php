<?php
namespace App\Domain\Repository;

use App\Domain\Model\GeneratedFile;
use App\Infrastructure\Database\MysqlConnection;

class ClinicRepository
{
    private MysqlConnection $connection;

    public function __construct(MysqlConnection $connection)
    {
        $this->connection = $connection;
    }

    public function save(GeneratedFile $file): void
    {
    }

    public function findByName(string $name): ?array
    {
        $sql = "SELECT * FROM clinics WHERE name = :name";
        $stmt = $this->connection->execute($sql, [':id' => $name]);
        $data = $stmt->fetchAll(2);

        if (!$data) {
            return null;
        }

        return $data;
    }

    public function findByTownState(string $town, string $state): ?array
    {
        $sql = "SELECT * FROM clinics WHERE LOWER(town) = :town AND LOWER(state) = :state";
        $stmt = $this->connection->execute($sql, [':town' => mb_strtolower($town), ':state' => mb_strtolower($state)]);
        $data = $stmt->fetchAll(2);

        if (!$data) {
            return null;
        }

        return $data;
    }
}