<?php
namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Services\FileManager;
use App\Services\SecurityService;
use App\Services\ValidationService;

class FileController
{
    public static function upload(): void
    {
        AuthMiddleware::requireAuth();
        SecurityService::verifyCsrfToken($_POST['csrf_token'] ?? null);

        if (empty($_FILES['file'])) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Aucun fichier reçu.';
            exit();
        }

        $uploadDir = __DIR__ . '/../../uploads';
        try {
            $fileId = FileManager::upload($_FILES['file'], $uploadDir);
        } catch (\Throwable $e) {
            header('HTTP/1.1 400 Bad Request');
            echo $e->getMessage();
            exit();
        }

        header('Content-Type: application/json');
        echo json_encode(['file_id' => $fileId], JSON_UNESCAPED_UNICODE);
    }
}
