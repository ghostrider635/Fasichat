<?php
session_start();
require_once __DIR__ . '/../config/Autoloader.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        // Pour le moment, simuler une connexion réussie pour tester
        $username = $_POST['username'] ?? '';
        $role = $_POST['role'] ?? 'etudiant';
        
        // Simuler un utilisateur
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        
        // Rediriger vers le dashboard approprié
        switch ($role) {
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
                header('Location: ../../public/pages/dashboard_vicedoyen.php');
                break;
            case 'apparitaire':
                header('Location: ../../public/pages/dashboard_apparitaire.php');
                break;
            default:
                header('Location: ../../public/pages/login.php?error=1');
                break;
        }
        exit();
    }
    
    if ($_POST['action'] === 'logout') {
        session_destroy();
        header('Location: ../../public/pages/login.php');
        exit();
    }
}

// Si aucune action valide, rediriger vers login
header('Location: ../../public/pages/login.php');
exit();
?>