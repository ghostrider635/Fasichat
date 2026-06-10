<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class FileRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function insertFile(string $nomOrigine, string $nomStockage, string $mimeType, int $taille, string $chemin): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO fichiers (nom_origine, nom_stockage, mime_type, taille, chemin, created_at) VALUES (:nom_origine, :nom_stockage, :mime_type, :taille, :chemin, NOW())'
        );
        $stmt->execute([
            'nom_origine' => $nomOrigine,
            'nom_stockage' => $nomStockage,
            'mime_type' => $mimeType,
            'taille' => $taille,
            'chemin' => $chemin,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM fichiers WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $file = $stmt->fetch();
        return $file ?: null;
    }
}
