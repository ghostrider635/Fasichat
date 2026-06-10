<?php
namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Services\SessionManager;
use App\Services\ValidationService;
use App\Repositories\ConvocationRepository;
use App\Services\SecurityService;

class ConvocationController
{
    public static function create(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::requireRole(['Doyen', 'Vice-Doyen']);
        SecurityService::verifyCsrfToken($_POST['csrf_token'] ?? null);

        $authorId = (int)SessionManager::get('user_id');
        $objet = ValidationService::sanitizeString((string)($_POST['objet'] ?? ''));
        $message = ValidationService::sanitizeString((string)($_POST['message'] ?? ''));
        $lieu = ValidationService::sanitizeString((string)($_POST['lieu'] ?? ''));
        $date = (string)($_POST['date'] ?? '');
        $heure = (string)($_POST['heure'] ?? '');
        $destinataires = $_POST['destinataires'] ?? [];

        if ($objet === '' || $lieu === '' || !ValidationService::validateDate($date) || !ValidationService::validateTime($heure)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Données de convocation invalides.';
            exit();
        }

        $destinations = is_array($destinataires) ? array_map([ValidationService::class, 'sanitizeString'], $destinataires) : [];
        if (empty($destinations)) {
            $destinations = ['Enseignant', 'Assistant'];
        }

        $convocationRepo = new ConvocationRepository();
        if (!$convocationRepo->createConvocation($authorId, $objet, $message, $lieu, $date, $heure, $destinations)) {
            header('HTTP/1.1 500 Internal Server Error');
            echo 'Impossible de creer la convocation.';
            exit();
        }

        if (self::isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            exit();
        }

        header('Location: dashboard_admin.php');
        exit();
    }

    private static function isAjax(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}
