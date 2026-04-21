<?php

namespace Hexlet\Code;

class CheckRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByUrlId(int $id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM url_checks WHERE url_id = :url_id ORDER BY created_at DESC");
        $stmt->execute(['url_id' => $id]);
        return $stmt->fetchAll() ?: [];
    }

    public function insert(int $id, int $status, ?string $h1, ?string $title, ?string $description): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO url_checks (url_id, status_code, h1, title, description, created_at)
            VALUES (:url_id, :status_code, :h1, :title, :description, NOW())
            RETURNING id"
        );
        $stmt->execute([
            'url_id' => $id,
            'status_code' => $status,
            'h1' => $h1,
            'title' => $title,
            'description' => $description,
        ]);
        $data = $stmt->fetchColumn();
        if ($data === false) {
            throw new \Exception("Failed to fetch");
        }
        return $data;
    }
}
