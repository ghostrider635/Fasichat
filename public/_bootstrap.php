<?php
require_once __DIR__ . '/../src/core/Config.php';
require_once __DIR__ . '/../src/core/Database.php';
require_once __DIR__ . '/../src/services/SessionManager.php';
require_once __DIR__ . '/../src/services/SecurityService.php';

use App\Core\Config;
use App\Core\Database;
use App\Services\AuthManager;
use App\Services\RoleService;
use App\Services\SecurityService;
use App\Services\SessionManager;

Config::load(__DIR__ . '/../config/app.php');

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

SessionManager::start();

function dashboardForRole(?string $role): string
{
    $roleKey = RoleService::normalize((string)$role);

    return match ($roleKey) {
        'Etudiant' => 'dashboard_etudiant.php',
        'Enseignant', 'Assistant' => 'dashboard_enseignant.php',
        'Doyen', 'Administrateur', 'Administrateur-Academique' => 'dashboard_admin.php',
        'Vice-Doyen' => 'dashboard_vicedoyen.php',
        'Apparitaire' => 'dashboard_apparitaire.php',
        default => 'login.php',
    };
}

function requireAuthPage(): void
{
    AuthManager::resumeRememberedSession();

    if (!SessionManager::get('user_id')) {
        header('Location: login.php');
        exit();
    }
}

function requireDashboardRoles(array $allowedRoles): void
{
    requireAuthPage();

    $role = (string)(SessionManager::get('role') ?? '');
    if (!RoleService::hasAccess($role, $allowedRoles)) {
        header('Location: ' . dashboardForRole($role));
        exit();
    }
}

function h(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function db(): ?PDO
{
    try {
        return Database::getInstance();
    } catch (Throwable $e) {
        return null;
    }
}

function fetchAllSafe(string $sql, array $params = []): array
{
    $pdo = db();
    if (!$pdo) {
        return [];
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function fetchOneSafe(string $sql, array $params = []): ?array
{
    $rows = fetchAllSafe($sql, $params);
    return $rows[0] ?? null;
}

function fetchValueSafe(string $sql, array $params = [], mixed $default = 0): mixed
{
    $pdo = db();
    if (!$pdo) {
        return $default;
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $value = $stmt->fetchColumn();
        return $value === false ? $default : $value;
    } catch (Throwable $e) {
        return $default;
    }
}

function currentUser(): ?array
{
    $userId = (int)(SessionManager::get('user_id') ?? 0);
    if ($userId <= 0) {
        return null;
    }

    return fetchOneSafe(
        'SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role_nom FROM utilisateurs u JOIN roles r ON r.id = u.role_id WHERE u.id = :id',
        ['id' => $userId]
    );
}

function initials(?array $user, string $fallback = 'FC'): string
{
    if (!$user) {
        return $fallback;
    }

    $first = mb_substr((string)($user['prenom'] ?? ''), 0, 1);
    $last = mb_substr((string)($user['nom'] ?? ''), 0, 1);
    $initials = mb_strtoupper($first . $last);
    return $initials !== '' ? $initials : $fallback;
}

function formatDateTime(?string $date): string
{
    if (!$date) {
        return '';
    }

    try {
        return (new DateTime($date))->format('d/m/Y H:i');
    } catch (Throwable $e) {
        return $date;
    }
}

function pageData(): array
{
    return (new \App\Services\DashboardService())->pageData();
}

