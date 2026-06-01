<?php
session_start();
require_once __DIR__ . '/../config/Autoloader.php';

// Charger la base de données
$db = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // ================= LOGIN =================
    if ($_POST['action'] === 'login') {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'etudiant';

        // Validation
        if (empty($username) || empty($password)) {
            header('Location: ../../public/pages/login.php?error=empty');
            exit();
        }

        // Configuration des rôles
        $tableInfo = [
            'etudiant' => [
                'table' => 'etudiants',
                'id_field' => 'id_etudiant',
                'matricule_field' => 'numero_etudiant'
            ],
            'enseignant' => [
                'table' => 'enseignants',
                'id_field' => 'id_enseignant',
                'matricule_field' => 'matricule'
            ],
            'assistant' => [
                'table' => 'assistants',
                'id_field' => 'id_assistant',
                'matricule_field' => 'matricule'
            ],
            'doyen' => [
                'table' => 'doyens',
                'id_field' => 'id_doyen',
                'matricule_field' => 'matricule'
            ],
            'vicedoyen' => [
                'table' => 'vice_doyens',
                'id_field' => 'id_vice_doyen',
                'matricule_field' => 'matricule'
            ],
            'apparitaire' => [
                'table' => 'apparitaires',
                'id_field' => 'id_apparitaire',
                'matricule_field' => 'matricule'
            ]
        ];

        // Normaliser le rôle reçu depuis le front (ex: 'vicedoyen' -> 'vice_doyen' en base)
        $roleAliases = [
            'vicedoyen' => 'vice_doyen',
            'vice-doyen' => 'vice_doyen',
            'vice_doyen' => 'vice_doyen',
            'doyen' => 'doyen',
            'enseignant' => 'enseignant',
            'assistant' => 'assistant',
            'etudiant' => 'etudiant',
            'apparitaire' => 'apparitaire'
        ];

        // Vérifier que le rôle est supporté par la configuration de tables
        if (!isset($tableInfo[$role])) {
            header('Location: ../../public/pages/login.php?error=invalid_role');
            exit();
        }

        $info = $tableInfo[$role];

        // Rôle tel qu'il est stocké en base (pour la comparaison u.type_utilisateur)
        $dbRole = $roleAliases[$role] ?? $role;

        try {

            // Requête utilisateur
            $sql = "SELECT 
                        u.id_utilisateur,
                        u.nom,
                        u.prenom,
                        u.email,
                        u.mot_de_passe_hash,
                        u.type_utilisateur,
                        u.statut,
                        t.{$info['matricule_field']} AS matricule
                    FROM utilisateurs u
                    JOIN {$info['table']} t
                        ON u.id_utilisateur = t.{$info['id_field']}
                    WHERE (t.{$info['matricule_field']} = :username OR u.email = :email)
                    AND u.type_utilisateur = :role";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':username' => $username,
                ':email' => $username,
                ':role' => $dbRole
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Utilisateur introuvable
            if (!$user) {
                header('Location: ../../public/pages/login.php?error=not_found');
                exit();
            }

            // Vérifier statut
            if ($user['statut'] !== 'actif') {
                header('Location: ../../public/pages/login.php?error=inactive');
                exit();
            }

            // Vérifier mot de passe
            if (!password_verify($password, $user['mot_de_passe_hash'])) {
                header('Location: ../../public/pages/login.php?error=password');
                exit();
            }

            // Création session
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['type_utilisateur'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['matricule'] = $user['matricule'];
            $_SESSION['login_time'] = time();

            // Redirection dashboard
            switch ($user['type_utilisateur']) {

                case 'etudiant':
                    header('Location: ../../public/pages/dashboard_etudiant.php');
                    break;

                case 'enseignant':
                case 'assistant':
                    header('Location: ../../public/pages/dashboard_enseignant.php');
                    break;

                case 'doyen':
                    header('Location: ../../public/pages/dashboard_doyen.php');
                    break;

                case 'vicedoyen':
                case 'vice_doyen':
                    header('Location: ../../public/pages/dashboard_vicedoyen.php');
                    break;

                case 'apparitaire':
                    header('Location: ../../public/pages/dashboard_apparitaire.php');
                    break;

                default:
                    header('Location: ../../public/pages/login.php?error=invalid_type');
                    break;
            }

            exit();

        } catch (PDOException $e) {

                // Log détaillé temporaire pour débogage
                $debugPath = __DIR__ . '/../../public/debug_auth.log';
                $debugData = [];
                $debugData[] = "[" . date('c') . "] PDOException: " . $e->getMessage();
                // Table existence
                try {
                    $tableExists = Database::getInstance()->tableExists($info['table']) ? 'yes' : 'no';
                } catch (Exception $ex) {
                    $tableExists = 'unknown: ' . $ex->getMessage();
                }
                $debugData[] = "Table checked: " . $info['table'] . " -> " . $tableExists;
                $debugData[] = "SQL: " . (isset($sql) ? $sql : 'n/a');
                $debugData[] = "Params: " . json_encode(['username' => $username, 'role' => $dbRole]);
                $debugData[] = "Stack: " . $e->getTraceAsString();
                file_put_contents($debugPath, implode("\n", $debugData) . "\n\n", FILE_APPEND | LOCK_EX);

                header('Location: ../../public/pages/login.php?error=database');
                exit();
        }
    }

    // ================= LOGOUT =================
    if ($_POST['action'] === 'logout') {

        session_destroy();

        header('Location: ../../public/pages/login.php');
        exit();
    }
}

// Aucune action valide
header('Location: ../../public/pages/login.php');
exit();
?>