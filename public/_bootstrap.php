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
    $user = currentUser();
    $stats = [
        'users' => (int)fetchValueSafe('SELECT COUNT(*) FROM utilisateurs'),
        'teachers' => (int)fetchValueSafe("SELECT COUNT(*) FROM utilisateurs u JOIN roles r ON r.id = u.role_id WHERE r.nom IN ('Enseignant', 'Assistant')"),
        'students' => (int)fetchValueSafe("SELECT COUNT(*) FROM utilisateurs u JOIN roles r ON r.id = u.role_id WHERE r.nom = 'Etudiant'"),
        'courses' => (int)fetchValueSafe('SELECT COUNT(*) FROM cours'),
        'promotions' => (int)fetchValueSafe('SELECT COUNT(*) FROM promotions'),
        'annonces' => (int)fetchValueSafe('SELECT COUNT(*) FROM annonces_valve'),
        'convocations' => (int)fetchValueSafe('SELECT COUNT(*) FROM convocations'),
        'messages' => (int)fetchValueSafe('SELECT COUNT(*) FROM messages'),
    ];

    return [
        'user' => $user,
        'userInitials' => initials($user),
        'csrfToken' => SecurityService::csrfToken(),
        'stats' => $stats,
        'users' => fetchAllSafe(
            'SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role_nom FROM utilisateurs u JOIN roles r ON r.id = u.role_id ORDER BY u.created_at DESC, u.id DESC LIMIT 12'
        ),
        'students' => fetchAllSafe(
            "SELECT u.id, u.nom, u.prenom, u.email,
                    COALESCE(GROUP_CONCAT(DISTINCT p.nom ORDER BY p.nom SEPARATOR ', '), 'Aucune promotion') AS promotion
             FROM utilisateurs u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN etudiants_promotions ep ON ep.etudiant_id = u.id
             LEFT JOIN promotions p ON p.id = ep.promotion_id
             WHERE r.nom = 'Etudiant'
             GROUP BY u.id, u.nom, u.prenom, u.email
             ORDER BY u.nom
             LIMIT 24"
        ),
        'teachers' => fetchAllSafe(
            "SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role_nom FROM utilisateurs u JOIN roles r ON r.id = u.role_id WHERE r.nom IN ('Enseignant', 'Assistant') ORDER BY u.nom LIMIT 24"
        ),
        'courses' => fetchAllSafe(
            'SELECT c.id,
                    c.nom,
                    c.code,
                    COALESCE(GROUP_CONCAT(DISTINCT p.nom ORDER BY p.nom SEPARATOR ", "), "Aucune promotion") AS promotion,
                    GROUP_CONCAT(DISTINCT p.id ORDER BY p.id SEPARATOR ",") AS promotion_ids,
                    COALESCE(GROUP_CONCAT(DISTINCT CONCAT(t.prenom, " ", t.nom) ORDER BY t.nom SEPARATOR ", "), "Aucun enseignant") AS enseignants,
                    COUNT(DISTINCT ep.etudiant_id) AS student_count,
                    COUNT(DISTINCT pm.id) AS message_count
             FROM cours c
             LEFT JOIN cours_promotions cp ON cp.cours_id = c.id
             LEFT JOIN promotions p ON p.id = cp.promotion_id
             LEFT JOIN etudiants_promotions ep ON ep.promotion_id = p.id
             LEFT JOIN enseignants_cours ec ON ec.cours_id = c.id
             LEFT JOIN utilisateurs t ON t.id = ec.enseignant_id
             LEFT JOIN mur_pedagogique m ON m.cours_id = c.id
             LEFT JOIN publications_mur pm ON pm.mur_id = m.id
             GROUP BY c.id, c.nom, c.code
             ORDER BY c.id
             LIMIT 24'
        ),
        'promotionsList' => fetchAllSafe(
            'SELECT p.id,
                    p.nom,
                    COUNT(DISTINCT ep.etudiant_id) AS student_count,
                    COUNT(DISTINCT cp.cours_id) AS course_count
             FROM promotions p
             LEFT JOIN etudiants_promotions ep ON ep.promotion_id = p.id
             LEFT JOIN cours_promotions cp ON cp.promotion_id = p.id
             GROUP BY p.id, p.nom
             ORDER BY p.nom'
        ),
        'annonces' => fetchAllSafe(
            'SELECT a.id, a.titre, a.contenu, a.categorie, a.created_at, u.nom, u.prenom FROM annonces_valve a JOIN utilisateurs u ON u.id = a.auteur_id ORDER BY a.created_at DESC LIMIT 20'
        ),
        'murPosts' => fetchAllSafe(
            'SELECT p.id, p.contenu, p.created_at, p.auteur_id, c.id AS course_id, c.nom AS cours_nom, u.nom, u.prenom FROM publications_mur p JOIN mur_pedagogique m ON m.id = p.mur_id JOIN cours c ON c.id = m.cours_id JOIN utilisateurs u ON u.id = p.auteur_id ORDER BY p.created_at DESC LIMIT 20'
        ),
        'messages' => fetchAllSafe(
            'SELECT m.id, m.contenu, m.created_at, u.nom, u.prenom, r.nom AS role_nom FROM messages m JOIN utilisateurs u ON u.id = m.expediteur_id JOIN roles r ON r.id = u.role_id ORDER BY m.created_at ASC LIMIT 40'
        ),
        'convocations' => fetchAllSafe(
            'SELECT c.id, c.objet, c.message, c.lieu, c.date_convocation, c.heure_convocation, c.created_at, u.nom, u.prenom FROM convocations c JOIN utilisateurs u ON u.id = c.expediteur_id ORDER BY c.created_at DESC LIMIT 10'
        ),
        'privateContacts' => $user ? fetchAllSafe(
            'SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role_nom
             FROM utilisateurs u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id <> :id
             ORDER BY
                CASE
                  WHEN r.nom IN ("Enseignant", "Assistant") THEN 1
                  WHEN r.nom = "Etudiant" THEN 2
                  ELSE 3
                END,
                u.nom,
                u.prenom
             LIMIT 20',
            ['id' => (int)$user['id']]
        ) : [],
    ];
}
