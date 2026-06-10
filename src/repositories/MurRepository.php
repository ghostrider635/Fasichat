<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class MurRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function createPost(int $authorId, int $courseId, string $content, ?int $fileId = null): bool
    {
        $murId = $this->findOrCreateMur($courseId);
        $stmt = $this->db->prepare(
            'INSERT INTO publications_mur (mur_id, auteur_id, contenu, fichier_id, created_at) VALUES (:mur_id, :auteur_id, :contenu, :fichier_id, NOW())'
        );

        return $stmt->execute([
            'mur_id' => $murId,
            'auteur_id' => $authorId,
            'contenu' => $content,
            'fichier_id' => $fileId,
        ]);
    }

    public function listByCourse(int $courseId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.nom, u.prenom FROM publications_mur p JOIN mur_pedagogique m ON p.mur_id = m.id JOIN utilisateurs u ON p.auteur_id = u.id WHERE m.cours_id = :cours_id ORDER BY p.created_at DESC'
        );
        $stmt->execute(['cours_id' => $courseId]);
        return $stmt->fetchAll();
    }

    private function findOrCreateMur(int $courseId): int
    {
        $stmt = $this->db->prepare('SELECT id FROM mur_pedagogique WHERE cours_id = :cours_id LIMIT 1');
        $stmt->execute(['cours_id' => $courseId]);
        $murId = $stmt->fetchColumn();
        if ($murId) {
            return (int)$murId;
        }

        $stmt = $this->db->prepare('INSERT INTO mur_pedagogique (cours_id) VALUES (:cours_id)');
        $stmt->execute(['cours_id' => $courseId]);
        return (int)$this->db->lastInsertId();
    }
}
