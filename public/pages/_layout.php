<?php
/**
 * Layout commun pour toutes les pages
 * Gère l'authentification et la structure de base
 */

// Démarrer la session si pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifier l'authentification et le rôle
 * @param string|null $requiredRole Rôle requis (null pour tout utilisateur connecté)
 * @return bool True si authentifié, false sinon
 */
function checkAuth($requiredRole = null) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    if ($requiredRole === null) {
        return true;
    }
    
    // Gestion spéciale pour enseignant/assistant
    if ($requiredRole === 'enseignant' && $_SESSION['role'] === 'assistant') {
        return true;
    }
    
    return $_SESSION['role'] === $requiredRole;
}

/**
 * Rediriger vers la page de login avec message d'erreur
 * @param string $message Message d'erreur optionnel
 */
function redirectToLogin($message = '') {
    if ($message) {
        $_SESSION['login_error'] = $message;
    }
    header('Location: login.php');
    exit();
}

/**
 * Requérir l'authentification (redirige si non authentifié)
 * @param string|null $requiredRole Rôle requis
 */
function requireAuth($requiredRole = null) {
    if (!checkAuth($requiredRole)) {
        $message = '';
        if ($requiredRole) {
            $message = "Accès réservé aux $requiredRole" . ($requiredRole === 'enseignant' ? 's et assistants' : 's');
        } else {
            $message = 'Veuillez vous connecter pour accéder à cette page';
        }
        redirectToLogin($message);
    }
}

/**
 * Obtenir le rôle de l'utilisateur actuel
 * @return string|null
 */
function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Obtenir l'ID de l'utilisateur actuel
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtenir le nom de l'utilisateur actuel
 * @return string|null
 */
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? null;
}

/**
 * Déconnexion sécurisée
 */
function logout() {
    // Détruire toutes les variables de session
    $_SESSION = array();
    
    // Détruire le cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Détruire la session
    session_destroy();
}

/**
 * Sanitizer pour prévenir XSS
 * @param string $input Texte à sanitizer
 * @return string Texte sanitizé
 */
function sanitize($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

/**
 * Générer un token CSRF
 * @return string Token CSRF
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier un token CSRF
 * @param string $token Token à vérifier
 * @return bool True si valide
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Configuration des cookies sécurisés
if (session_status() === PHP_SESSION_ACTIVE) {
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => $cookieParams['lifetime'],
        'path' => $cookieParams['path'],
        'domain' => $cookieParams['domain'],
        'secure' => true, // Require HTTPS
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'Strict' // Prevent CSRF
    ]);
}
?>