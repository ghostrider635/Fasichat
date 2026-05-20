<?php
// Configuration des chemins pour le frontend
define('BASE_URL', '/Fasichat/frontend/pages/');
define('ASSETS_URL', '/Fasichat/frontend/assets/');
define('CSS_URL', ASSETS_URL . 'css/dashboard/');
define('JS_URL', ASSETS_URL . 'js/dashboard/');

// Fonction pour générer les URLs
function url($page) {
    return BASE_URL . $page;
}

function css($file) {
    return CSS_URL . $file;
}

function js($file) {
    return JS_URL . $file;
}

// Fonction pour vérifier l'authentification
function requireAuth($requiredRole = null) {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . url('login.php'));
        exit();
    }
    
    if ($requiredRole && $_SESSION['role'] !== $requiredRole) {
        // Pour certains rôles, vérifier plusieurs possibilités
        if ($requiredRole === 'enseignant' && $_SESSION['role'] === 'assistant') {
            return; // Les assistants ont accès au dashboard enseignant
        }
        
        header('Location: ' . url('login.php'));
        exit();
    }
}
?>