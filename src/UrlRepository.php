<?php

namespace Hexlet\Code;

class UrlRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM urls ORDER BY created_at DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
