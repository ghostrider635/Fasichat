<?php
/**
 * Autoloader pour les classes PHP
 */

spl_autoload_register(function ($className) {
    // Convertir les namespaces en chemin de fichier
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    
    // Chemins possibles
    $paths = [
        __DIR__ . '/../classes/' . $className . '.php',
        __DIR__ . '/' . $className . '.php',
        __DIR__ . '/../controllers/' . $className . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
    
    // Si la classe n'est pas trouvée, on peut loguer l'erreur
    error_log("Classe non trouvée : $className");
});

// Inclure les fichiers de configuration
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Config.php';

// Définir le fuseau horaire
date_default_timezone_set('Europe/Paris');

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonction utilitaire pour debug
function debug($data, $exit = true) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    if ($exit) exit();
}
?>