<?php
namespace App\Services;

class SessionManager {
    public static function start(): void {
        if (session_status() == PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 0); // Passer à 1 en production (HTTPS)
            ini_set('session.use_only_cookies', 1);
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    public static function destroy(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}