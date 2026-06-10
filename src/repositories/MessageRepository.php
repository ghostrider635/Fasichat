<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class MessageRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function fetchConversationMessages(int $conversationId): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, u.nom, u.prenom, u.role_id, r.nom AS role_nom FROM messages m JOIN utilisateurs u ON m.expediteur_id = u.id JOIN roles r ON u.role_id = r.id WHERE m.conversation_id = :conversation_id ORDER BY m.created_at ASC'
        );
        $stmt->execute(['conversation_id' => $conversationId]);
        return $stmt->fetchAll();
    }

    public function createMessage(int $conversationId, int $senderId, string $content, ?int $fileId = null): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO messages (conversation_id, expediteur_id, contenu, fichier_id, created_at) VALUES (:conversation_id, :expediteur_id, :contenu, :fichier_id, NOW())'
        );

        return $stmt->execute([
            'conversation_id' => $conversationId,
            'expediteur_id' => $senderId,
            'contenu' => $content,
            'fichier_id' => $fileId,
        ]);
    }
}
