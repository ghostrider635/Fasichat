<?php
namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Services\SessionManager;
use App\Services\ValidationService;
use App\Repositories\MurRepository;
use App\Services\SecurityService;

class MurController
{
    public static function publish(): void
    {
        AuthMiddleware::requireAuth();
        SecurityService::verifyCsrfToken($_POST['csrf_token'] ?? null);

        $authorId = (int)SessionManager::get('user_id');
        $courseId = ValidationService::sanitizeInteger($_POST['course_id'] ?? 0);
        $content = ValidationService::sanitizeString((string)($_POST['content'] ?? ''));

        if ($courseId <= 0 || $content === '') {
            header('HTTP/1.1 400 Bad Request');
            echo 'Le contenu ou le cours est manquant.';
            exit();
        }

        $murRepo = new MurRepository();
        $murRepo->createPost($authorId, $courseId, $content, null);

        if (self::isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            exit();
        }

        header('Location: dashboard_enseignant.php');
        exit();
    }

    private static function isAjax(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}
