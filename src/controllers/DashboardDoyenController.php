<?php
namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

use App\Core\Database;

class DashboardDoyenController
{
    private static function json(array $payload): void
    {
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public static function stats(): void
    {

        AuthMiddleware::requireAuth();
        RoleMiddleware::requireRole(['Doyen', 'Vice-Doyen', 'Administrateur', 'Administrateur-Academique']);

        $db = Database::getInstance();

        $row = $db->query('SELECT
            (SELECT COUNT(*) FROM utilisateurs) AS users_total,
            (SELECT COUNT(*) FROM utilisateurs u JOIN roles r ON u.role_id = r.id WHERE r.nom = "Enseignant") AS teachers_total,
            (SELECT COUNT(*) FROM utilisateurs u JOIN roles r ON u.role_id = r.id WHERE r.nom = "Assistant") AS assistants_total,
            (SELECT COUNT(*) FROM cours) AS courses_total,
            (SELECT COUNT(*) FROM promotions) AS promotions_total,
            (SELECT COUNT(*) FROM etudiants_promotions) AS students_in_promotions,
            (SELECT COUNT(*) FROM convocations) AS convocations_total,
            (SELECT COUNT(*) FROM annonces_valve) AS annonces_total
        ')->fetch();

        self::json(['success' => true, 'stats' => $row ?: []]);
    }


    public static function recentUsers(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::requireRole(['Doyen', 'Vice-Doyen', 'Administrateur', 'Administrateur-Academique']);

        $db = Database::getInstance();

        $stmt = $db->query('SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role_nom
            FROM utilisateurs u
            JOIN roles r ON u.role_id = r.id
            ORDER BY u.id DESC
            LIMIT 8');

        $users = $stmt->fetchAll();

        self::json(['success' => true, 'users' => $users]);
    }


    public static function recentActivity(): void
    {

        AuthMiddleware::requireAuth();
        RoleMiddleware::requireRole(['Doyen', 'Vice-Doyen', 'Administrateur', 'Administrateur-Academique']);

        $db = Database::getInstance();

        $stmt = $db->query(
            '(SELECT 
                "convocation" AS type,
                c.objet AS title,
                CONCAT("Par ", u.nom, " ", u.prenom) AS subtitle,
                c.created_at AS created_at
              FROM convocations c
              JOIN utilisateurs u ON u.id = c.expediteur_id
              ORDER BY c.created_at DESC
              LIMIT 3)
             UNION ALL
             (SELECT 
                "valve" AS type,
                a.titre AS title,
                CONCAT("Par ", u.nom, " ", u.prenom) AS subtitle,
                a.created_at AS created_at
              FROM annonces_valve a
              JOIN utilisateurs u ON u.id = a.auteur_id
              ORDER BY a.created_at DESC
              LIMIT 3)
             ORDER BY created_at DESC
             LIMIT 6'
        );

        $items = $stmt->fetchAll();

        self::json(['success' => true, 'items' => $items]);
    }

    public static function coursesList(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::requireRole(['Doyen', 'Vice-Doyen', 'Administrateur', 'Administrateur-Academique']);

        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT
                c.id,
                c.nom,
                c.code,
                GROUP_CONCAT(DISTINCT p.nom ORDER BY p.nom SEPARATOR ", ") AS promotions,
                (SELECT COUNT(*)
                   FROM etudiants_promotions ep
                  WHERE ep.promotion_id IN (
                      SELECT cp.promotion_id
                        FROM cours_promotions cp
                       WHERE cp.cours_id = c.id
                  )
                ) AS student_count
             FROM cours c
             LEFT JOIN cours_promotions cp ON cp.cours_id = c.id
             LEFT JOIN promotions p ON p.id = cp.promotion_id
             GROUP BY c.id, c.nom, c.code
             ORDER BY c.id DESC
             LIMIT 20'
        );

        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['promotion'] = $r['promotions'] ? $r['promotions'] : 'Aucune promotion';
            unset($r['promotions']);
        }

        self::json(['success' => true, 'courses' => $rows]);
    }


    public static function promotionsList(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::requireRole(['Doyen', 'Vice-Doyen', 'Administrateur', 'Administrateur-Academique']);

        $db = Database::getInstance();
        $stmt = $db->query('SELECT id, nom FROM promotions ORDER BY nom');
        $promos = $stmt->fetchAll();
        self::json(['success' => true, 'promotions' => $promos]);
    }

    public static function usersList(): void
    {
        AuthMiddleware::requireAuth();
        RoleMiddleware::requireRole(['Doyen', 'Vice-Doyen', 'Administrateur', 'Administrateur-Academique']);

        $role = isset($_GET['role']) ? (string)$_GET['role'] : null;
        $allowed = $role ? [ $role ] : null;

        if ($allowed) {
            $stmt = $db = Database::getInstance()->prepare(
                'SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role_nom
                 FROM utilisateurs u
                 JOIN roles r ON r.id = u.role_id
                 WHERE r.nom = :role
                 ORDER BY u.nom'
            );
            $stmt->execute(['role' => $allowed[0]]);
            $users = $stmt->fetchAll();
        } else {
            $db = Database::getInstance();
            $stmt = $db->query(
                'SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role_nom
                 FROM utilisateurs u
                 JOIN roles r ON r.id = u.role_id
                 ORDER BY u.id DESC LIMIT 50'
            );
            $users = $stmt->fetchAll();
        }

        self::json(['success' => true, 'users' => $users]);
    }
}


