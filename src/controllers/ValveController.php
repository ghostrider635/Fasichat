<?php
namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Services\SessionManager;
use App\Services\ValidationService;
use App\Repositories\ValveRepository;
use App\Services\SecurityService;

class ValveController
{
    public static function list(): void
    {
        AuthMiddleware::requireAuth();

        $repository = new ValveRepository();
        $annonces = $repository->listAll();
        header('Content-Type: application/json');
        echo json_encode($annonces, JSON_UNESCAPED_UNICODE);
    }

    public static function create(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::requireRole(['Apparitaire']);
        SecurityService::verifyCsrfToken($_POST['csrf_token'] ?? null);

        $titre = ValidationService::sanitizeString((string)($_POST['titre'] ?? ''));
        $contenu = ValidationService::sanitizeString((string)($_POST['contenu'] ?? ''));
        $categorie = ValidationService::sanitizeString((string)($_POST['categorie'] ?? 'Information'));
        $auteurId = (int)SessionManager::get('user_id');

        if ($titre === '' || $contenu === '') {
            header('HTTP/1.1 400 Bad Request');
            echo 'Titre et contenu requis.';
            exit();
        }

        $repository = new ValveRepository();
        $repository->create($titre, $contenu, $auteurId, $categorie);

        if (self::isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            exit();
        }

        header('Location: valve.php');
        exit();
    }

    public static function update(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::requireRole(['Apparitaire']);
        SecurityService::verifyCsrfToken($_POST['csrf_token'] ?? null);

        $id = ValidationService::sanitizeInteger($_POST['id'] ?? 0);
        $titre = ValidationService::sanitizeString((string)($_POST['titre'] ?? ''));
        $contenu = ValidationService::sanitizeString((string)($_POST['contenu'] ?? ''));
        $categorie = ValidationService::sanitizeString((string)($_POST['categorie'] ?? 'Information'));

        if ($id <= 0 || $titre === '' || $contenu === '') {
            header('HTTP/1.1 400 Bad Request');
            echo 'Données manquantes pour la mise à jour.';
            exit();
        }

        $repository = new ValveRepository();
        $repository->update($id, $titre, $contenu, $categorie);

        if (self::isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            exit();
        }

        header('Location: valve.php');
        exit();
    }

    public static function delete(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::requireRole(['Apparitaire']);
        SecurityService::verifyCsrfToken($_POST['csrf_token'] ?? null);

        $id = ValidationService::sanitizeInteger($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Identifiant d\'annonce invalide.';
            exit();
        }

        $repository = new ValveRepository();
        $repository->delete($id);

        if (self::isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            exit();
        }

        header('Location: valve.php');
        exit();
    }

    private static function isAjax(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}
