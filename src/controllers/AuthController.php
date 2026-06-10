<?php
namespace App\Controllers;

use App\Core\Database;
use App\Services\AuthManager;
use App\Services\RoleService;
use App\Services\SecurityService;
use App\Services\ValidationService;
use App\Repositories\UserRepository;

class AuthController
{
    public static function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: login.php');
            exit();
        }

        $email = ValidationService::sanitizeEmail((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $selectedRole = (string)($_POST['role_selectionne'] ?? '');
        $rememberMe = isset($_POST['remember_me']);

        $result = AuthManager::login($email, $password, $selectedRole, $rememberMe);
        $user = $result['user'];
        if ($user) {
            header('Location: ' . $user->getDashboardUrl());
            exit();
        }

        $error = $result['error'] === 'role' ? 'role' : '1';
        header('Location: login.php?error=' . $error);
        exit();
    }

    public static function logout(): void
    {
        AuthManager::logout();
    }

    public static function resetPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: forgot_password.php');
            exit();
        }

        SecurityService::verifyCsrfToken($_POST['csrf_token'] ?? null);

        $email = ValidationService::sanitizeEmail((string)($_POST['email'] ?? ''));
        $role = (string)($_POST['role_selectionne'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['password_confirm'] ?? '');

        if ($email === '' || strlen($password) < 8 || $password !== $confirm) {
            header('Location: forgot_password.php?error=invalid');
            exit();
        }

        $repository = new UserRepository();
        $user = $repository->findByEmail($email);
        if (!$user || $role === '' || !RoleService::hasAccess($user['role_nom'], [$role])) {
            header('Location: forgot_password.php?error=role');
            exit();
        }

        $repository->updatePasswordByEmail($email, $password);
        header('Location: login.php?reset=1');
        exit();
    }

    public static function contactAdmin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: contact_admin.php');
            exit();
        }

        SecurityService::verifyCsrfToken($_POST['csrf_token'] ?? null);

        $nom = ValidationService::sanitizeString((string)($_POST['nom'] ?? ''));
        $prenom = ValidationService::sanitizeString((string)($_POST['prenom'] ?? ''));
        $email = ValidationService::sanitizeEmail((string)($_POST['email'] ?? ''));
        $role = ValidationService::sanitizeString((string)($_POST['role_selectionne'] ?? ''));
        $message = ValidationService::sanitizeString((string)($_POST['message'] ?? ''));

        if ($nom === '' || $prenom === '' || $email === '' || $role === '' || $message === '') {
            header('Location: contact_admin.php?error=1');
            exit();
        }

        $db = Database::getInstance();
        $db->exec(
            'CREATE TABLE IF NOT EXISTS demandes_administration (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL,
                role_demande VARCHAR(80) NOT NULL,
                message TEXT NOT NULL,
                statut VARCHAR(30) NOT NULL DEFAULT "nouvelle",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $stmt = $db->prepare(
            'INSERT INTO demandes_administration (nom, prenom, email, role_demande, message)
             VALUES (:nom, :prenom, :email, :role, :message)'
        );
        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'role' => $role,
            'message' => $message,
        ]);

        header('Location: contact_admin.php?sent=1');
        exit();
    }
}
