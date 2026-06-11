<?php
namespace App\Services;

use App\Core\Database;
use PDO;

class RateLimiter
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_TIME_WINDOW = 900; // 15 minutes en secondes
    private const MAX_REQUESTS_PER_MINUTE = 60;
    
    public static function checkLogin(string $email, string $ip): bool
    {
        $db = Database::getInstance();
        
        // Nettoyer les vieilles tentatives
        $db->exec(
            'DELETE FROM login_attempts 
             WHERE created_at < DATE_SUB(NOW(), INTERVAL ' . self::LOGIN_TIME_WINDOW . ' SECOND)'
        );
        
        // Compter les tentatives récentes
        $stmt = $db->prepare(
            'SELECT COUNT(*) as attempts 
             FROM login_attempts 
             WHERE (email = :email OR ip_address = :ip) 
             AND created_at > DATE_SUB(NOW(), INTERVAL ' . self::LOGIN_TIME_WINDOW . ' SECOND)'
        );
        
        $stmt->execute([
            'email' => $email,
            'ip' => $ip
        ]);
        
        $result = $stmt->fetch();
        return ($result['attempts'] ?? 0) < self::MAX_LOGIN_ATTEMPTS;
    }
    
    public static function recordLoginAttempt(string $email, bool $success): void
    {
        $db = Database::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $db->prepare(
            'INSERT INTO login_attempts (email, ip_address, user_agent, success) 
             VALUES (:email, :ip, :user_agent, :success)'
        );
        
        $stmt->execute([
            'email' => $email,
            'ip' => $ip,
            'user_agent' => substr($userAgent, 0, 255),
            'success' => $success ? 1 : 0
        ]);
    }
    
    public static function isBlocked(string $email, string $ip): bool
    {
        return !self::checkLogin($email, $ip);
    }
    
    public static function getRemainingTime(string $email, string $ip): int
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare(
            'SELECT TIMESTAMPDIFF(SECOND, MIN(created_at), NOW()) as time_passed 
             FROM login_attempts 
             WHERE (email = :email OR ip_address = :ip) 
             AND created_at > DATE_SUB(NOW(), INTERVAL ' . self::LOGIN_TIME_WINDOW . ' SECOND)'
        );
        
        $stmt->execute(['email' => $email, 'ip' => $ip]);
        $result = $stmt->fetch();
        
        $timePassed = $result['time_passed'] ?? 0;
        return max(0, self::LOGIN_TIME_WINDOW - $timePassed);
    }
}