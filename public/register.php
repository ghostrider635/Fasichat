<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FasiChat Classroom — Inscription Etudiant</title>
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
  <div class="left-panel">
    <div class="brand">
      <div class="brand-logo">🧾</div>
      <div class="brand-name">Fasi<span>Chat</span></div>
      <div class="brand-sub">Inscription — rôle étudiant</div>
    </div>

    <div class="features">
      <div class="feature-item">
        <div class="feature-icon">✅</div>
        <div class="feature-text">
          <h4>Compte gratuit</h4>
          <p>Création rapide pour les étudiants</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">🔐</div>
        <div class="feature-text">
          <h4>Accès sécurisé</h4>
          <p>CSRF + validation côté serveur</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">📣</div>
        <div class="feature-text">
          <h4>Messagerie & Valve</h4>
          <p>Accès aux fonctionnalités selon le rôle</p>
        </div>
      </div>
    </div>

    <div class="left-bottom">© 2026 FasiChat Classroom.</div>
  </div>

  <div class="right-panel">
    <div class="form-header">
      <h2>Créer un compte</h2>
      <p>Vous allez créer un compte avec le rôle <b>Etudiant</b>.</p>

      <?php if(isset($_GET['error'])): ?>
        <p style="color: #ff6b6b; font-family: 'Sora', sans-serif; font-size: 0.9rem; margin-top: 10px;">
          <?php
            $map = [
              'missing' => 'Champs manquants.',
              'invalid' => 'Informations invalides.',
              'exists' => 'Cet email est déjà utilisé.',
              'password' => 'Mot de passe invalide.',
              'role' => 'Rôle non autorisé pour l’inscription. Contactez l’administration.',
            ];
            echo htmlspecialchars((string)($map[$_GET['error']] ?? 'Erreur'), ENT_QUOTES, 'UTF-8');
          ?>
        </p>
      <?php endif; ?>

      <?php if(isset($_GET['created'])): ?>
        <p style="color: #22c55e; font-family: 'Sora', sans-serif; font-size: 0.9rem; margin-top: 10px;">
          Compte créé. Vous pouvez vous connecter.
        </p>
      <?php endif; ?>
    </div>

    <form action="index.php?action=register" method="POST">
      <input type="hidden" name="csrf_token" value="<?= h(\App\Services\SecurityService::csrfToken()) ?>">

      <input type="hidden" name="role_selectionne" id="role_selectionne" value="Etudiant">

      <div class="form-group">
        <label class="form-label">Nom</label>
        <div class="input-wrapper">
          <span class="input-icon">🧑‍🎓</span>
          <input type="text" name="nom" class="form-input" placeholder="Ex: Sarr" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Prénom</label>
        <div class="input-wrapper">
          <span class="input-icon">🧑‍🎓</span>
          <input type="text" name="prenom" class="form-input" placeholder="Ex: Fatou" required>
        </div>
      </div>

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
          <input type="password" name="password" class="form-input" minlength="12" placeholder="Au moins 12 caractères" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Confirmer le mot de passe</label>
        <div class="input-wrapper">
          <span class="input-icon">✓</span>
          <input type="password" name="password_confirm" class="form-input" minlength="12" placeholder="Retapez le mot de passe" required>
        </div>
      </div>

      <button type="submit" class="btn-login">Créer mon compte →</button>
    </form>

    <div class="register-link">
      Déjà un compte ? <a href="login.php">Se connecter</a>
    </div>
  </div>
</div>
</body>
</html>

