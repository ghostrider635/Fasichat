<?php
namespace App\Controllers;

use App\Services\SessionManager;

class DashboardController
{
    public static function home(): void
    {
        SessionManager::start();
        \App\Services\AuthManager::resumeRememberedSession();

        if (SessionManager::get('user_id')) {
            $role = SessionManager::get('role');
            $roleKey = \App\Services\RoleService::normalize((string)$role);
            $page = match ($roleKey) {
                'Etudiant' => 'dashboard_etudiant.php',
                'Enseignant' => 'dashboard_enseignant.php',
                'Assistant' => 'dashboard_enseignant.php',
                'Doyen' => 'dashboard_admin.php',
                'Vice-Doyen' => 'dashboard_vicedoyen.php',
                'Administrateur-Academique' => 'dashboard_admin.php',
                'Apparitaire' => 'dashboard_apparitaire.php',
                default => 'login.php',
            };
            header('Location: ' . $page);
            exit();
        }

        header('Location: login.php');
        exit();
    }

    public static function show(): void
    {
        self::home();
    }
}
