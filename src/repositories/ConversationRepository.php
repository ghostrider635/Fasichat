<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class ConversationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findConversation(int $senderId, int $receiverId, string $type, ?int $promotionId = null): ?array
    {
        $query = 'SELECT * FROM conversations WHERE type = :type AND ((expediteur_id = :sender_a AND destinataire_id = :receiver_a) OR (expediteur_id = :receiver_b AND destinataire_id = :sender_b))';
        if ($promotionId !== null) {
            $query .= ' AND promotion_id = :promotion_id';
        }

        $stmt = $this->db->prepare($query);
        $params = [
            'type' => $type,
            'sender_a' => $senderId,
            'receiver_a' => $receiverId,
            'sender_b' => $senderId,
            'receiver_b' => $receiverId,
        ];
        if ($promotionId !== null) {
            $params['promotion_id'] = $promotionId;
        }

        $stmt->execute($params);
        $conversation = $stmt->fetch();
        return $conversation ?: null;
    }

    public function createConversation(string $type, ?int $promotionId = null, ?int $senderId = null, ?int $receiverId = null): int
    {
        $stmt = $this->db->prepare('INSERT INTO conversations (type, promotion_id, expediteur_id, destinataire_id) VALUES (:type, :promotion_id, :expediteur_id, :destinataire_id)');
        $stmt->execute([
            'type' => $type,
            'promotion_id' => $promotionId,
            'expediteur_id' => $senderId,
            'destinataire_id' => $receiverId,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getOrCreatePrivateConversation(int $senderId, int $receiverId): int
    {
        $conversation = $this->findConversation($senderId, $receiverId, 'prive');
        if ($conversation) {
            return (int)$conversation['id'];
        }

        return $this->createConversation('prive', null, $senderId, $receiverId);
    }

    public function userCanAccessConversation(int $conversationId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM conversations
             WHERE id = :id AND (expediteur_id = :user_id OR destinataire_id = :user_id)'
        );
        $stmt->execute([
            'id' => $conversationId,
            'user_id' => $userId,
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function listPrivateForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT c.id AS conversation_id,
                    other_user.id AS user_id,
                    other_user.nom,
                    other_user.prenom,
                    other_user.email,
                    r.nom AS role_nom,
                    last_msg.contenu AS last_message,
                    last_msg.created_at AS last_message_at
             FROM conversations c
             JOIN utilisateurs other_user
               ON other_user.id = CASE
                    WHEN c.expediteur_id = :user_id_a THEN c.destinataire_id
                    ELSE c.expediteur_id
                  END
             JOIN roles r ON r.id = other_user.role_id
             LEFT JOIN messages last_msg ON last_msg.id = (
                SELECT m.id FROM messages m
                WHERE m.conversation_id = c.id
                ORDER BY m.created_at DESC, m.id DESC
                LIMIT 1
             )
             WHERE c.type = "prive"
               AND (c.expediteur_id = :user_id_b OR c.destinataire_id = :user_id_c)
             ORDER BY COALESCE(last_msg.created_at, "1970-01-01") DESC, c.id DESC'
        );
        $stmt->execute([
            'user_id_a' => $userId,
            'user_id_b' => $userId,
            'user_id_c' => $userId,
        ]);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM conversations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $conversation = $stmt->fetch();
        return $conversation ?: null;
    }
}
