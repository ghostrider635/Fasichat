<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FasiChat Classroom — Connexion</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/login.css">
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

      <!-- Affichage dynamique d'une erreur si la connexion échoue -->
      <?php if(isset($_GET['reset'])): ?>
        <p style="color: #22c55e; font-family: 'Sora', sans-serif; font-size: 0.9rem; margin-top: 10px;">
          Mot de passe reinitialise. Vous pouvez vous connecter.
        </p>
      <?php endif; ?>
      <?php if(isset($_GET['error'])): ?>
        <p style="color: #ff6b6b; font-family: 'Sora', sans-serif; font-size: 0.9rem; margin-top: 10px;">
          <?= $_GET['error'] === 'role' ? 'Role incorrect pour cet utilisateur.' : 'Identifiant ou mot de passe ou rôle incorrect.' ?>
        </p>
      <?php endif; ?>
    </div>

    <div class="role-selector">
      <!-- Les boutons appellent la fonction JS qui mettra à jour notre input caché -->
      <button class="role-btn active" type="button" onclick="setRole(this, 'Etudiant')">Étudiant</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Enseignant')">Enseignant</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Assistant')">Assistant</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Doyen')">Doyen</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Vice-Doyen')">Vice-Doyen</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Apparitaire')">Apparitaire</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Administrateur-Academique')">Admin</button>
    </div>

    <!-- CORRECTION : Redirection vers notre routeur unique index.php en méthode POST -->
    <form action="index.php?action=login" method="POST">

      <!-- CORRECTION : Stockage du rôle sélectionné pour traitement ou double vérification côté serveur -->
      <input type="hidden" name="role_selectionne" id="role_selectionne" value="Etudiant">

      <div class="form-group">
        <label class="form-label">Adresse email</label>
        <div class="input-wrapper">
          <span class="input-icon">👤</span>
          <input type="email" name="email" class="form-input" placeholder="Ex: utilisateur@fasi.edu" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Mot de passe</label>
        <div class="input-wrapper">
          <span class="input-icon">🔑</span>
          <!-- CORRECTION : Ajout de l'attribut name="password" -->
          <input type="password" name="password" class="form-input" placeholder="••••••••" required>
        </div>
      </div>

      <div class="form-row">
        <label class="checkbox-wrap">
          <input type="checkbox" name="remember_me">
          <span class="custom-check">✓</span>
          <span class="checkbox-label">Se souvenir de moi</span>
        </label>
        <a href="forgot_password.php" class="forgot-link">Mot de passe oublié ?</a>
      </div>

      <button type="submit" class="btn-login">Se connecter →</button>
    </form>

    <div class="divider">ou</div>
    <div class="register-link">
      Première connexion ? <a href="register.php">Inscription (Etudiant)</a> ou <a href="contact_admin.php">Contactez l'administration</a>
    </div>
  </div>
</div>

<script src="assets/js/login.js"></script>
</body>
</html>
