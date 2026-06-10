<?php
namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Services\SessionManager;
use App\Services\ValidationService;
use App\Repositories\MessageRepository;
use App\Repositories\ConversationRepository;
use App\Services\SecurityService;

class MessageController
{
    public static function thread(): void
    {
        AuthMiddleware::requireAuth();

        $currentUserId = (int)SessionManager::get('user_id');
        $receiverId = ValidationService::sanitizeInteger($_GET['with'] ?? 0);
        if ($receiverId <= 0 || $receiverId === $currentUserId) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Discussion invalide.';
            exit();
        }

        $otherUser = (new \App\Repositories\UserRepository())->findById($receiverId);
        if (!$otherUser) {
            header('HTTP/1.1 404 Not Found');
            echo 'Utilisateur introuvable.';
            exit();
        }

        $conversationRepo = new ConversationRepository();
        $conversationId = $conversationRepo->getOrCreatePrivateConversation($currentUserId, $receiverId);
        $messages = (new MessageRepository())->fetchConversationMessages($conversationId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'conversation_id' => $conversationId,
            'current_user_id' => $currentUserId,
            'other_user' => [
                'id' => (int)$otherUser['id'],
                'nom' => $otherUser['nom'],
                'prenom' => $otherUser['prenom'],
                'email' => $otherUser['email'],
                'role_nom' => $otherUser['role_nom'],
            ],
            'messages' => $messages,
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    public static function send(): void
    {
        AuthMiddleware::requireAuth();
        SecurityService::verifyCsrfToken($_POST['csrf_token'] ?? null);

        $senderId = (int)SessionManager::get('user_id');
        $receiverId = ValidationService::sanitizeInteger($_POST['receiver_id'] ?? 0);
        $content = ValidationService::sanitizeString((string)($_POST['content'] ?? ''));

        if ($receiverId <= 0 || $content === '') {
            header('HTTP/1.1 400 Bad Request');
            echo 'Paramètres manquants pour envoyer le message.';
            exit();
        }

        $conversationRepo = new ConversationRepository();
        $messageRepo = new MessageRepository();

        if ($receiverId <= 0) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Destinataire invalide pour le message privé.';
            exit();
        }

        $conversationId = $conversationRepo->getOrCreatePrivateConversation($senderId, $receiverId);
        $messageRepo->createMessage($conversationId, $senderId, $content);

        if (self::isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'conversation_id' => $conversationId], JSON_UNESCAPED_UNICODE);
            exit();
        }

        header('Location: dashboard_etudiant.php');
        exit();
    }

    private static function isAjax(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}
