<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class ValveRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function listAll(): array
    {
        $stmt = $this->db->query(
            'SELECT a.*, u.nom, u.prenom FROM annonces_valve a JOIN utilisateurs u ON a.auteur_id = u.id ORDER BY a.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function create(string $titre, string $contenu, int $auteurId, string $categorie = 'Information'): bool
    {
        $valveId = $this->findOrCreateDefaultValve();
        $stmt = $this->db->prepare(
            'INSERT INTO annonces_valve (valve_id, titre, contenu, categorie, auteur_id, created_at) VALUES (:valve_id, :titre, :contenu, :categorie, :auteur_id, NOW())'
        );
        return $stmt->execute([
            'valve_id' => $valveId,
            'titre' => $titre,
            'contenu' => $contenu,
            'categorie' => $categorie,
            'auteur_id' => $auteurId,
        ]);
    }

    public function update(int $id, string $titre, string $contenu, string $categorie): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE annonces_valve SET titre = :titre, contenu = :contenu, categorie = :categorie WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'titre' => $titre,
            'contenu' => $contenu,
            'categorie' => $categorie,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM annonces_valve WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    private function findOrCreateDefaultValve(): int
    {
        $id = $this->db->query('SELECT id FROM valve ORDER BY id ASC LIMIT 1')->fetchColumn();
        if ($id) {
            return (int)$id;
        }

        $stmt = $this->db->prepare('INSERT INTO valve (nom) VALUES (:nom)');
        $stmt->execute(['nom' => 'Panneau FasiChat']);
        return (int)$this->db->lastInsertId();
    }
}
