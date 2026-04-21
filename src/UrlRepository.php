<?php

namespace Hexlet\Code;

class UrlRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM urls WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getByName(string $name): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM urls WHERE name = :name");
        $stmt->execute(['name' => $name]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                urls.id,
                urls.name,
                urls.created_at,
                url_checks.status_code,
                url_checks.created_at as last_checked_at
            FROM urls
            LEFT JOIN url_checks ON urls.id = url_checks.url_id
                AND url_checks.id = (
                    SELECT MAX(id)
                    FROM url_checks
                    WHERE url_id = urls.id
                )
            ORDER BY last_checked_at DESC NULLS LAST
        ");
        $stmt->execute();
        $result = $stmt->fetchAll();
        if ($result === false) {
            throw new \Exception("Failed to fetch");
        }
        return $result;
    }

    public function insert(string $name): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO urls (name, created_at) VALUES (:name, NOW()) RETURNING id");
        $stmt->execute(['name' => $name]);
        $result = $stmt->fetchColumn();
        if ($result === false) {
            throw new \Exception("Failed to fetch");
        }
        return $result;
    }
}
