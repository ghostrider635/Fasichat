<?php
// Configuration des chemins
define('BASE_URL', '/Fasichat/');
define('ASSETS_URL', BASE_URL . 'public/assets/');
define('CSS_URL', ASSETS_URL . 'css/');
define('JS_URL', ASSETS_URL . 'js/');

// Résout l'URL d'un asset CSS ou JS en cherchant dans plusieurs dossiers
function assetUrl(string $type, string $file): string {
    $extension = $type === 'css' ? '.css' : '.js';
    $assetDir = __DIR__ . '/assets/' . $type . '/';
    $assetUrl = ASSETS_URL . $type . '/';

    if (substr($file, -strlen($extension)) !== $extension) {
        $file .= $extension;
    }

    // Chemin direct dans le dossier racine assets/css ou assets/js
    $directPath = $assetDir . $file;
    if (file_exists($directPath)) {
        return $assetUrl . $file;
    }

    // Dossier du même nom que le fichier, ex. assets/js/valve/valve.js
    $sameFolderPath = $assetDir . dirname($file) . '/' . basename($file);
    if ($file === basename($file) && file_exists($assetDir . basename($file, $extension) . '/' . basename($file))) {
        return $assetUrl . basename($file, $extension) . '/' . basename($file);
    }

    // Dossier dashboard pour les assets dashboard spécifiques
    $dashboardPath = $assetDir . 'dashboard/' . $file;
    if (file_exists($dashboardPath)) {
        return $assetUrl . 'dashboard/' . $file;
    }

    // Fallback vers le dossier racine
    return $assetUrl . $file;
}

// Fonction pour générer les URLs
function url($page) {
    // Si la page a déjà l'extension .php, ne pas l'ajouter
    if (substr($page, -4) === '.php') {
        return BASE_URL . 'public/pages/' . $page;
    }
    return BASE_URL . 'public/pages/' . $page . '.php';
}

function css($file) {
    return assetUrl('css', $file);
}

function js($file) {
    return assetUrl('js', $file);
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