<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class ConvocationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function createConvocation(int $authorId, string $objet, string $message, string $lieu, string $date, string $heure, array $destinations): bool
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO convocations (expediteur_id, objet, message, lieu, date_convocation, heure_convocation, created_at) VALUES (:expediteur_id, :objet, :message, :lieu, :date_convocation, :heure_convocation, NOW())'
            );
            $stmt->execute([
                'expediteur_id' => $authorId,
                'objet' => $objet,
                'message' => $message,
                'lieu' => $lieu,
                'date_convocation' => $date,
                'heure_convocation' => $heure,
            ]);

            $convocationId = (int)$this->db->lastInsertId();
            $recipientIds = $this->resolveDestinataires($destinations);

            $insertDest = $this->db->prepare(
                'INSERT INTO convocations_destinataires (convocation_id, destinataire_id, lu) VALUES (:convocation_id, :destinataire_id, 0)'
            );
            foreach ($recipientIds as $destinataireId) {
                $insertDest->execute([
                    'convocation_id' => $convocationId,
                    'destinataire_id' => $destinataireId,
                ]);
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }

    private function resolveDestinataires(array $destinations): array
    {
        $userIds = [];
        $roles = [];
        foreach ($destinations as $destination) {
            $destination = trim($destination);
            if ($destination === '') {
                continue;
            }
            if (ctype_digit((string)$destination)) {
                $userIds[] = (int)$destination;
            } else {
                $roles[] = $destination;
            }
        }

        if (!empty($roles)) {
            $placeholders = implode(',', array_fill(0, count($roles), '?'));
            $stmt = $this->db->prepare(
                "SELECT u.id FROM utilisateurs u JOIN roles r ON u.role_id = r.id WHERE r.nom IN ($placeholders)"
            );
            $stmt->execute($roles);
            foreach ($stmt->fetchAll() as $row) {
                $userIds[] = (int)$row['id'];
            }
        }

        return array_unique($userIds);
    }

    public function listForRole(string $role): array
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, u.nom AS author_nom, u.prenom AS author_prenom FROM convocations c JOIN utilisateurs u ON c.expediteur_id = u.id JOIN convocations_destinataires cd ON cd.convocation_id = c.id JOIN utilisateurs d ON d.id = cd.destinataire_id JOIN roles r ON r.id = d.role_id WHERE r.nom = :role ORDER BY c.date_convocation DESC, c.heure_convocation DESC'
        );
        $stmt->execute(['role' => $role]);
        return $stmt->fetchAll();
    }
}
