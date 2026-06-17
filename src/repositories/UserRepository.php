<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;
use App\Entities\Utilisateur;

class UserRepository
{
    private PDO $db;

    private function getRoleIdByNameInternal(string $roleNom): ?int
    {
        // RoleService::normalize() gère surtout les accents et espaces => tiret.
        // Ici on s'appuie sur la même normalisation pour matcher la valeur.
        $normalized = \App\Services\RoleService::normalize($roleNom);

        $stmt = $this->db->prepare('SELECT id FROM roles WHERE nom = :nom OR nom = :nom2 LIMIT 1');
        $stmt->execute([
            'nom' => $normalized,
            'nom2' => $roleNom,
        ]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }


    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.nom AS role_nom FROM utilisateurs u JOIN roles r ON u.role_id = r.id WHERE u.email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.nom AS role_nom FROM utilisateurs u JOIN roles r ON u.role_id = r.id WHERE u.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role_id) VALUES (:nom, :prenom, :email, :password, :role_id)'
        );
        return $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role_id' => $data['role_id'],
        ]);
    }

    public function getRoleIdByName(string $roleNom): ?int
    {
        return $this->getRoleIdByNameInternal($roleNom);
    }


    public function updatePasswordByEmail(string $email, string $password): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE utilisateurs SET mot_de_passe = :password WHERE email = :email'
        );
        return $stmt->execute([
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);
    }

    public function listAll(): array
    {
        $stmt = $this->db->query(
            'SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role_nom FROM utilisateurs u JOIN roles r ON u.role_id = r.id ORDER BY u.nom'
        );
        return $stmt->fetchAll();
    }

    public function getUsersByRole(string $role): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.nom, u.prenom, u.email FROM utilisateurs u JOIN roles r ON u.role_id = r.id WHERE r.nom = :role'
        );
        $stmt->execute(['role' => $role]);
        return $stmt->fetchAll();
    }
}
