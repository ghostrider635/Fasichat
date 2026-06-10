<?php
namespace App\Services;

class SecurityService
{
    public static function setSecureHeaders(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: same-origin');
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        $valid = is_string($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
        if (!$valid) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Jeton de securite invalide.';
            exit();
        }

        return true;
    }
}
