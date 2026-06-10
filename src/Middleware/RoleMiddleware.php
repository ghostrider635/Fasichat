<?php
namespace App\Middleware;

use App\Services\SessionManager;
use App\Services\RoleService;

class RoleMiddleware
{
    public static function requireRole(array $allowedRoles): void
    {
        SessionManager::start();
        $role = SessionManager::get('role');
        if (!$role || !RoleService::hasAccess($role, $allowedRoles)) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Accès refusé.';
            exit();
        }
    }
}
