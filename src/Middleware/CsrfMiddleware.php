<?php
namespace App\Middleware;

use App\Services\SecurityService;

class CsrfMiddleware
{
    public static function validateToken(?string $token): void
    {
        if (!SecurityService::verifyCsrfToken($token)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Jeton CSRF invalide.';
            exit();
        }
    }
}
