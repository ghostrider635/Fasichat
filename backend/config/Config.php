<?php
/**
 * Configuration de la base de données FasiChat Classroom
 */

class Config {
    // Configuration de la base de données
    const DB_HOST = 'localhost';
    const DB_NAME = 'fasichat_db';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_CHARSET = 'utf8mb4';
    
    // Configuration de l'application
    const APP_NAME = 'FasiChat Classroom';
    const APP_VERSION = '1.0.0';
    const APP_ENV = 'development'; // development, testing, production
    
    // Configuration des sessions
    const SESSION_NAME = 'FASICHAT_SESSION';
    const SESSION_LIFETIME = 1800; // 30 minutes en secondes
    const SESSION_PATH = '/';
    const SESSION_DOMAIN = '';
    const SESSION_SECURE = false;
    const SESSION_HTTPONLY = true;
    
    // Configuration des fichiers uploadés
    const UPLOAD_MAX_SIZE = 20971520; // 20 Mo en octets
    const UPLOAD_ALLOWED_TYPES = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'document' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
        'archive' => ['zip', 'rar', '7z'],
        'video' => ['mp4', 'avi', 'mov', 'wmv'],
        'audio' => ['mp3', 'wav', 'ogg']
    ];
    const UPLOAD_DIR = __DIR__ . '/../../public/uploads/';
    
    // Configuration de sécurité
    const PASSWORD_ALGO = PASSWORD_BCRYPT;
    const PASSWORD_COST = 12;
    const CSRF_TOKEN_NAME = 'csrf_token';
    const CSRF_TOKEN_LIFETIME = 3600; // 1 heure
    
    // Configuration des rôles
    const ROLES = [
        'etudiant' => 1,
        'enseignant' => 2,
        'assistant' => 3,
        'doyen' => 4,
        'vice_doyen' => 5,
        'apparitaire' => 6
    ];
    
    // Configuration des permissions
    const PERMISSIONS = [
        'etudiant' => [
            'send_private_message' => true,
            'send_public_message' => true,
            'view_courses' => true,
            'upload_files' => true,
            'view_valve' => true,
            'create_convocation' => false,
            'manage_valve' => false
        ],
        'enseignant' => [
            'send_private_message' => true,
            'send_public_message' => true,
            'view_courses' => true,
            'upload_files' => true,
            'view_valve' => true,
            'create_convocation' => false,
            'manage_valve' => false,
            'manage_course' => true,
            'manage_pedagogical_wall' => true
        ],
        'assistant' => [
            'send_private_message' => true,
            'send_public_message' => true,
            'view_courses' => true,
            'upload_files' => true,
            'view_valve' => true,
            'create_convocation' => false,
            'manage_valve' => false,
            'manage_course' => true,
            'manage_pedagogical_wall' => true
        ],
        'doyen' => [
            'send_private_message' => true,
            'send_public_message' => true,
            'view_courses' => true,
            'upload_files' => true,
            'view_valve' => true,
            'create_convocation' => true,
            'manage_valve' => false,
            'manage_all_courses' => true,
            'view_all_messages' => true
        ],
        'vice_doyen' => [
            'send_private_message' => true,
            'send_public_message' => true,
            'view_courses' => true,
            'upload_files' => true,
            'view_valve' => true,
            'create_convocation' => true,
            'manage_valve' => false,
            'manage_all_courses' => true,
            'view_all_messages' => true
        ],
        'apparitaire' => [
            'send_private_message' => true,
            'send_public_message' => true,
            'view_courses' => true,
            'upload_files' => true,
            'view_valve' => true,
            'create_convocation' => false,
            'manage_valve' => true,
            'publish_valve_announcements' => true
        ]
    ];
    
    // Configuration des chemins
    const BASE_URL = 'http://localhost/Fasichat/';
    const ASSETS_URL = self::BASE_URL . 'public/assets/';
    const UPLOAD_URL = self::BASE_URL . 'public/uploads/';
    
    // Configuration du logging
    const LOG_DIR = __DIR__ . '/../../logs/';
    const LOG_LEVEL = 'DEBUG'; // DEBUG, INFO, WARNING, ERROR
    
    /**
     * Vérifie si l'environnement est en développement
     */
    public static function isDevelopment(): bool {
        return self::APP_ENV === 'development';
    }
    
    /**
     * Vérifie si l'environnement est en production
     */
    public static function isProduction(): bool {
        return self::APP_ENV === 'production';
    }
    
    /**
     * Retourne le DSN pour PDO
     */
    public static function getDSN(): string {
        return "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::DB_CHARSET;
    }
    
    /**
     * Retourne les options PDO
     */
    public static function getPDOOptions(): array {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::DB_CHARSET
        ];
    }
    
    /**
     * Retourne la configuration de session
     */
    public static function getSessionConfig(): array {
        return [
            'name' => self::SESSION_NAME,
            'lifetime' => self::SESSION_LIFETIME,
            'path' => self::SESSION_PATH,
            'domain' => self::SESSION_DOMAIN,
            'secure' => self::SESSION_SECURE,
            'httponly' => self::SESSION_HTTPONLY
        ];
    }
    
    /**
     * Vérifie si un type de fichier est autorisé
     */
    public static function isFileTypeAllowed(string $extension): bool {
        $extension = strtolower($extension);
        
        foreach (self::UPLOAD_ALLOWED_TYPES as $types) {
            if (in_array($extension, $types)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Retourne le dossier d'upload pour un type de fichier
     */
    public static function getUploadDirForType(string $type): string {
        $type = strtolower($type);
        $dirs = [
            'image' => 'images/',
            'document' => 'documents/',
            'archive' => 'archives/',
            'video' => 'videos/',
            'audio' => 'audio/'
        ];
        
        return self::UPLOAD_DIR . ($dirs[$type] ?? 'others/');
    }
    
    /**
     * Hash un mot de passe
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, self::PASSWORD_ALGO, ['cost' => self::PASSWORD_COST]);
    }
    
    /**
     * Vérifie un mot de passe
     */
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * Génère un token CSRF
     */
    public static function generateCSRFToken(): string {
        if (!isset($_SESSION[self::CSRF_TOKEN_NAME])) {
            $_SESSION[self::CSRF_TOKEN_NAME] = [
                'token' => bin2hex(random_bytes(32)),
                'expires' => time() + self::CSRF_TOKEN_LIFETIME
            ];
        }
        
        return $_SESSION[self::CSRF_TOKEN_NAME]['token'];
    }
    
    /**
     * Vérifie un token CSRF
     */
    public static function verifyCSRFToken(string $token): bool {
        if (!isset($_SESSION[self::CSRF_TOKEN_NAME])) {
            return false;
        }
        
        $storedToken = $_SESSION[self::CSRF_TOKEN_NAME];
        
        if (time() > $storedToken['expires']) {
            unset($_SESSION[self::CSRF_TOKEN_NAME]);
            return false;
        }
        
        return hash_equals($storedToken['token'], $token);
    }
    
    /**
     * Redirige vers une URL
     */
    public static function redirect(string $url, int $statusCode = 302): void {
        header("Location: " . $url, true, $statusCode);
        exit();
    }
    
    /**
     * Retourne l'URL complète
     */
    public static function url(string $path = ''): string {
        return self::BASE_URL . ltrim($path, '/');
    }
    
    /**
     * Retourne l'URL des assets
     */
    public static function asset(string $path): string {
        return self::ASSETS_URL . ltrim($path, '/');
    }
    
    /**
     * Retourne l'URL d'upload
     */
    public static function uploadUrl(string $path): string {
        return self::UPLOAD_URL . ltrim($path, '/');
    }
}