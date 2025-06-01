<?php
namespace App\Domain\Repository;

use App\Domain\Model\GeneratedFile;
use App\Infrastructure\Database\MysqlConnection;

class FileRepository
{
    private MysqlConnection $connection;

    public function __construct(MysqlConnection $connection)
    {
        $this->connection = $connection;
    }

    public function save(GeneratedFile $file): void
    {
        $sql = "INSERT INTO generated_files 
                (id, filename, template, data, file_path, created_at) 
                VALUES (:id, :filename, :template, :data, :file_path, :created_at)";

        $params = [
            ':id' => $file->getId(),
            ':filename' => $file->getFilename(),
            ':template' => $file->getTemplate(),
            ':data' => json_encode($file->getData()),
            ':file_path' => $file->getFilePath(),
            ':created_at' => $file->getCreatedAt()->format('Y-m-d H:i:s')
        ];

        $this->connection->execute($sql, $params);
    }

    public function find(string $id): ?GeneratedFile
    {
        $sql = "SELECT * FROM generated_files WHERE id = :id";
        $stmt = $this->connection->execute($sql, [':id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new GeneratedFile(
            $data['id'],
            $data['filename'],
            $data['template'],
            json_decode($data['data'], true),
            $data['file_path'],
            new \DateTime($data['created_at'])
        );
    }

    public function deleteOldFiles(int $days = 30): int
    {
        $date = new \DateTime("-$days days");
        $sql = "DELETE FROM generated_files WHERE created_at < :date";
        $stmt = $this->connection->execute($sql, [':date' => $date->format('Y-m-d H:i:s')]);
        return $stmt->rowCount();
    }
}