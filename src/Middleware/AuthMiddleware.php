<?php
namespace App\Middleware;

use App\Services\AuthManager;
use App\Services\SessionManager;

class AuthMiddleware
{
    public static function requireAuth(): void
    {
        SessionManager::start();
        AuthManager::resumeRememberedSession();
        if (!SessionManager::get('user_id')) {
            header('Location: login.php');
            exit();
        }
    }
}
