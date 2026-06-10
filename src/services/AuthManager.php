<?php
namespace App\Services;

use App\Core\Database;
use App\Repositories\UserRepository;
use App\Entities\Etudiant;
use App\Entities\Enseignant;
use App\Entities\Assistant;
use App\Entities\Doyen;
use App\Entities\ViceDoyen;
use App\Entities\Apparitaire;
use App\Entities\AdministrateurAcademique;

class AuthManager
{
    private const REMEMBER_COOKIE = 'fasichat_remember';
    private const REMEMBER_DAYS = 30;

    private static ?UserRepository $repository = null;

    private static function repository(): UserRepository
    {
        if (self::$repository === null) {
            self::$repository = new UserRepository();
        }
        return self::$repository;
    }

    public static function login(string $email, string $password, string $selectedRole = '', bool $rememberMe = false): array
    {
        $email = ValidationService::sanitizeEmail($email);
        $userRow = self::repository()->findByEmail($email);

        if (!$userRow || !password_verify($password, $userRow['mot_de_passe'])) {
            return ['user' => null, 'error' => 'credentials'];
        }

        if ($selectedRole === '' || !RoleService::hasAccess($userRow['role_nom'], [$selectedRole])) {
            return ['user' => null, 'error' => 'role'];
        }

        SessionManager::start();
        self::setSessionFromUserRow($userRow);

        if ($rememberMe) {
            self::rememberUser((int)$userRow['id']);
        } else {
            self::clearRememberCookie();
        }

        return ['user' => self::instantiateUser($userRow), 'error' => null];
    }

    public static function logout(): void
    {
        self::forgetRememberedUser();
        SessionManager::destroy();
        header('Location: login.php');
        exit();
    }

    public static function resumeRememberedSession(): bool
    {
        SessionManager::start();
        if (SessionManager::get('user_id')) {
            return true;
        }

        $token = $_COOKIE[self::REMEMBER_COOKIE] ?? '';
        if (!is_string($token) || $token === '') {
            return false;
        }

        $stmt = Database::getInstance()->prepare(
            'SELECT s.id AS session_id, u.*, r.nom AS role_nom
             FROM sessions_utilisateur s
             JOIN utilisateurs u ON u.id = s.utilisateur_id
             JOIN roles r ON r.id = u.role_id
             WHERE s.token = :token AND s.expire_at > NOW()
             LIMIT 1'
        );
        $stmt->execute(['token' => hash('sha256', $token)]);
        $userRow = $stmt->fetch();

        if (!$userRow) {
            self::clearRememberCookie();
            return false;
        }

        self::setSessionFromUserRow($userRow);
        return true;
    }

    private static function setSessionFromUserRow(array $userRow): void
    {
        SessionManager::set('user_id', (int)$userRow['id']);
        SessionManager::set('role', $userRow['role_nom']);
        SessionManager::set('user_email', $userRow['email']);
    }

    private static function rememberUser(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = (new \DateTimeImmutable('+' . self::REMEMBER_DAYS . ' days'))->format('Y-m-d H:i:s');

        $stmt = Database::getInstance()->prepare(
            'INSERT INTO sessions_utilisateur (utilisateur_id, token, ip_address, user_agent, expire_at)
             VALUES (:utilisateur_id, :token, :ip_address, :user_agent, :expire_at)'
        );
        $stmt->execute([
            'utilisateur_id' => $userId,
            'token' => hash('sha256', $token),
            'ip_address' => substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            'user_agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            'expire_at' => $expiresAt,
        ]);

        setcookie(self::REMEMBER_COOKIE, $token, [
            'expires' => time() + (self::REMEMBER_DAYS * 86400),
            'path' => '/',
            'httponly' => true,
            'secure' => false,
            'samesite' => 'Lax',
        ]);
    }

    private static function forgetRememberedUser(): void
    {
        $token = $_COOKIE[self::REMEMBER_COOKIE] ?? '';
        if (is_string($token) && $token !== '') {
            $stmt = Database::getInstance()->prepare('DELETE FROM sessions_utilisateur WHERE token = :token');
            $stmt->execute(['token' => hash('sha256', $token)]);
        }

        self::clearRememberCookie();
    }

    private static function clearRememberCookie(): void
    {
        setcookie(self::REMEMBER_COOKIE, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'secure' => false,
            'samesite' => 'Lax',
        ]);
    }

    private static function instantiateUser(array $userRow): \App\Entities\Utilisateur
    {
        $roleKey = RoleService::normalize($userRow['role_nom']);
        return match ($roleKey) {
            'Etudiant' => new Etudiant((int)$userRow['id'], $userRow['nom'], $userRow['prenom'], $userRow['email'], $userRow['mot_de_passe'], $userRow['role_nom']),
            'Enseignant' => new Enseignant((int)$userRow['id'], $userRow['nom'], $userRow['prenom'], $userRow['email'], $userRow['mot_de_passe'], $userRow['role_nom']),
            'Assistant' => new Assistant((int)$userRow['id'], $userRow['nom'], $userRow['prenom'], $userRow['email'], $userRow['mot_de_passe'], $userRow['role_nom']),
            'Doyen' => new Doyen((int)$userRow['id'], $userRow['nom'], $userRow['prenom'], $userRow['email'], $userRow['mot_de_passe'], $userRow['role_nom']),
            'Vice-Doyen' => new ViceDoyen((int)$userRow['id'], $userRow['nom'], $userRow['prenom'], $userRow['email'], $userRow['mot_de_passe'], $userRow['role_nom']),
            'Apparitaire' => new Apparitaire((int)$userRow['id'], $userRow['nom'], $userRow['prenom'], $userRow['email'], $userRow['mot_de_passe'], $userRow['role_nom']),
            'Administrateur-Academique' => new AdministrateurAcademique((int)$userRow['id'], $userRow['nom'], $userRow['prenom'], $userRow['email'], $userRow['mot_de_passe'], $userRow['role_nom']),
            'Administrateur' => new AdministrateurAcademique((int)$userRow['id'], $userRow['nom'], $userRow['prenom'], $userRow['email'], $userRow['mot_de_passe'], $userRow['role_nom']),
            default => new Etudiant((int)$userRow['id'], $userRow['nom'], $userRow['prenom'], $userRow['email'], $userRow['mot_de_passe'], $userRow['role_nom']),
        };
    }

    public static function restrictTo(array $allowedRoles): void
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
