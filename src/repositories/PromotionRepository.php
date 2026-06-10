<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class PromotionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM promotions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $promo = $stmt->fetch();
        return $promo ?: null;
    }

    public function listAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM promotions ORDER BY nom');
        return $stmt->fetchAll();
    }
}
