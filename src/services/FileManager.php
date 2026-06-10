<?php
namespace App\Services;

use App\Core\Config;
use App\Repositories\FileRepository;
use Exception;

class FileManager
{
    private static ?FileRepository $repository = null;

    public static function upload(array $fileProfile, string $uploadDir): int
    {
        if ($fileProfile['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erreur système de téléversement.');
        }

        if ($fileProfile['size'] > Config::get('upload.max_size')) {
            throw new Exception('Le fichier dépasse la limite autorisée de 20 Mo.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileProfile['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, Config::get('upload.allowed_mime'), true)) {
            throw new Exception('Format MIME non autorisé pour l’espace académique.');
        }

        $extension = strtolower(pathinfo($fileProfile['name'], PATHINFO_EXTENSION));
        $uniqueName = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $uniqueName;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            throw new Exception('Impossible de créer le dossier de stockage.');
        }

        if (!move_uploaded_file($fileProfile['tmp_name'], $targetPath)) {
            throw new Exception('Impossible d’enregistrer le fichier.');
        }

        if (str_starts_with($mimeType, 'image/')) {
            CompressionManager::compressImage($targetPath, $mimeType);
        }

        if (str_starts_with($mimeType, 'video/')) {
            CompressionManager::compressVideo($targetPath);
        }

        return self::repository()->insertFile(
            htmlspecialchars($fileProfile['name'], ENT_QUOTES, 'UTF-8'),
            $uniqueName,
            $mimeType,
            (int)filesize($targetPath),
            $targetPath
        );
    }

    private static function repository(): FileRepository
    {
        if (self::$repository === null) {
            self::$repository = new FileRepository();
        }
        return self::$repository;
    }
}
