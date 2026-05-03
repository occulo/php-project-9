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
        $urls = $this->pdo->query("SELECT * FROM urls")->fetchAll();
        $checks = $this->pdo->query(
            "SELECT DISTINCT ON (url_id) url_id, status_code, created_at as last_checked_at
            FROM url_checks
            ORDER BY url_id, created_at DESC"
        )->fetchAll(\PDO::FETCH_UNIQUE);

        $result = array_map(function ($url) use ($checks) {
            $check = $checks[$url['id']] ?? [];
            return array_merge($url, $check);
        }, $urls);
        usort($result, fn($a, $b) => strtotime($b['last_checked_at']) - strtotime($a['last_checked_at']));

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
