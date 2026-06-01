<?php
session_start();
require_once '../config.php';

// Si l'utilisateur est déjà connecté, rediriger vers le dashboard approprié
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'etudiant':
            header('Location: ' . url('dashboard_etudiant.php'));
            break;
        case 'enseignant':
        case 'assistant':
            header('Location: ' . url('dashboard_enseignant.php'));
            break;
        case 'doyen':
            header('Location: ' . url('dashboard_doyen.php'));
            break;
        case 'vicedoyen':
            header('Location: ' . url('dashboard_vicedoyen.php'));
            break;
        case 'apparitaire':
            header('Location: ' . url('dashboard_apparitaire.php'));
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FasiChat Classroom — Connexion</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo css('login.css'); ?>">
</head>
<body>
<div class="bg-layer"></div>
<div class="grid-lines"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<div class="login-wrapper">
  <!-- Left Panel -->
  <div class="left-panel">
    <div class="brand">
      <div class="brand-logo">💬</div>
      <div class="brand-name">Fasi<span>Chat</span></div>
      <div class="brand-sub">Classroom Edition &mdash; Plateforme Académique</div>
    </div>

    <div class="features">
      <div class="feature-item">
        <div class="feature-icon">📚</div>
        <div class="feature-text">
          <h4>Cours & Promotions</h4>
          <p>Regroupez étudiants et enseignants par cours et promotion</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">🔒</div>
        <div class="feature-text">
          <h4>Messagerie Sécurisée</h4>
          <p>Messages privés, publics et mur pédagogique selon les rôles</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">📁</div>
        <div class="feature-text">
          <h4>Partage de Fichiers</h4>
          <p>PDF, vidéos, documents jusqu'à 20 Mo avec compression auto</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">📣</div>
        <div class="feature-text">
          <h4>Onglet Valve</h4>
          <p>Annonces institutionnelles visibles par toute la communauté</p>
        </div>
      </div>
    </div>

    <div class="left-bottom">© 2026 FasiChat Classroom. Tous droits réservés.</div>
  </div>

  <!-- Right Panel -->
  <div class="right-panel">
    <div class="form-header">
      <h2>Bienvenue FreeDom</h2>
      <p>Connectez-vous à votre espace académique</p>
    </div>

    <?php
    // Afficher les erreurs de login (param ?error=...)
    if (isset($_GET['error'])) {
      $err = $_GET['error'];
      $messages = [
        'empty' => 'Veuillez remplir tous les champs.',
        'invalid_role' => 'Rôle invalide sélectionné.',
        'not_found' => 'Utilisateur introuvable (vérifiez matricule/email et rôle).',
        'inactive' => 'Compte inactif, contactez l\'administration.',
        'password' => 'Mot de passe incorrect.',
        'database' => 'Erreur base de données. Voir les logs.',
        'invalid_type' => 'Type d\'utilisateur non supporté.'
      ];
      $msg = $messages[$err] ?? 'Erreur inconnue.';
      echo "<div class=\"login-error\">" . htmlspecialchars($msg) . "</div>";
    }
    ?>

    <div class="role-selector">
      <button class="role-btn active" onclick="setRole(this)">Étudiant</button>
      <button class="role-btn" onclick="setRole(this)">Enseignant</button>
      <button class="role-btn" onclick="setRole(this)">Assistant</button>
      <button class="role-btn" onclick="setRole(this)">Doyen</button>
      <button class="role-btn" onclick="setRole(this)">Vice-Doyen</button>
      <button class="role-btn" onclick="setRole(this)">Apparitaire</button>
    </div>

    <form action="<?php echo BASE_URL . 'backend/controllers/AuthController.php'; ?>" method="post">
      <input type="hidden" name="action" value="login">
      <div class="form-group">
        <label class="form-label" for="roleInput">Rôle</label>
        <select name="role" id="roleInput" class="form-input">
          <option value="etudiant">Étudiant</option>
          <option value="enseignant">Enseignant</option>
          <option value="assistant">Assistant</option>
          <option value="doyen">Doyen</option>
          <option value="vice_doyen">Vice-Doyen</option>
          <option value="apparitaire">Apparitaire</option>
        </select>
      </div>
      
      <div class="form-group">
        <label class="form-label">Identifiant / Matricule</label>
        <div class="input-wrapper">
          <span class="input-icon">👤</span>
          <input type="text" class="form-input" name="username" placeholder="Ex: ET2024001" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Mot de passe</label>
        <div class="input-wrapper">
          <span class="input-icon">🔑</span>
          <input type="password" class="form-input" name="password" placeholder="••••••••" required>
        </div>
      </div>

      <div class="form-row">
        <label class="checkbox-wrap">
          <input type="checkbox" name="remember">
          <span class="custom-check">✓</span>
          <span class="checkbox-label">Se souvenir de moi</span>
        </label>
        <a href="#" class="forgot-link">Mot de passe oublié ?</a>
      </div>

      <button type="submit" class="btn-login">Se connecter →</button>
    </form>

    <div class="divider">ou</div>
    <div class="register-link">
      Première connexion ? <a href="#">Contactez l'administration</a>
    </div>
  </div>
</div>

<script src="<?php echo js('login.js'); ?>"></script>
</body>
</html>