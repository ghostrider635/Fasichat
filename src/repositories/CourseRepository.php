<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class CourseRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM cours WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $course = $stmt->fetch();
        return $course ?: null;
    }

    public function listByTeacher(int $teacherId): array
    {
        $stmt = $this->db->prepare(
            'SELECT c.* FROM cours c JOIN enseignants_cours ec ON c.id = ec.cours_id WHERE ec.enseignant_id = :teacher_id'
        );
        $stmt->execute(['teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }
}
