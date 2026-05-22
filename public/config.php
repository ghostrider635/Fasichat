<?php
// Configuration des chemins
define('BASE_URL', '/Fasichat/');
define('ASSETS_URL', BASE_URL . 'public/assets/');
define('CSS_URL', ASSETS_URL . 'css/dashboard/');
define('JS_URL', ASSETS_URL . 'js/dashboard/');

// Fonction pour générer les URLs
function url($page) {
    return BASE_URL . 'public/pages/' . $page . '.php';
}

function css($file) {
    // Si le fichier a déjà l'extension .css, ne pas l'ajouter
    if (substr($file, -4) === '.css') {
        return CSS_URL . $file;
    }
    return CSS_URL . $file . '.css';
}

function js($file) {
    // Si le fichier a déjà l'extension .js, ne pas l'ajouter
    if (substr($file, -3) === '.js') {
        return JS_URL . $file;
    }
    return JS_URL . $file . '.js';
}

// Fonction pour vérifier l'authentification
function requireAuth($requiredRole = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . url('login'));
        exit();
    }
    
    if ($requiredRole) {
        // Pour certains rôles, vérifier plusieurs possibilités
        if ($requiredRole === 'enseignant' && $_SESSION['role'] === 'assistant') {
            return; // Les assistants ont accès au dashboard enseignant
        }
        
        if ($_SESSION['role'] !== $requiredRole) {
            header('Location: ' . url('login'));
            exit();
        }
    }
}

// Fonction pour rediriger selon le rôle
function redirectByRole() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        return url('login');
    }
    
    switch ($_SESSION['role']) {
        case 'etudiant':
            return url('dashboard_etudiant');
        case 'enseignant':
        case 'assistant':
            return url('dashboard_enseignant');
        case 'doyen':
            return url('dashboard_doyen');
        case 'vicedoyen':
            return url('dashboard_vicedoyen');
        case 'apparitaire':
            return url('dashboard_apparitaire');
        default:
            return url('login');
    }
}
?>