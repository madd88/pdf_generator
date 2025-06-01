<?php
namespace App\Infrastructure\Database;

use PDO;
use PDOException;
use PDOStatement;

class MysqlConnection
{
    private PDO $connection;

    public function __construct(
        string $host,
        string $database,
        string $username,
        string $password
    ) {
        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new \RuntimeException("MySQL connection failed: " . $e->getMessage());
        }
    }

    public function prepare(string $sql): PDOStatement
    {
        return $this->connection->prepare($sql);
    }

    public function execute(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}