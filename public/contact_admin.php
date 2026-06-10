<?php require __DIR__ . '/_bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FasiChat Classroom - Contact administration</title>
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
      <div class="brand-logo">📨</div>
      <div class="brand-name">Fasi<span>Chat</span></div>
      <div class="brand-sub">Demande d'acces</div>
    </div>
    <div class="features">
      <div class="feature-item">
        <div class="feature-icon">🏛</div>
        <div class="feature-text">
          <h4>Administration</h4>
          <p>Votre demande est enregistree pour traitement par l'equipe academique.</p>
        </div>
      </div>
    </div>
    <div class="left-bottom">© 2026 FasiChat Classroom.</div>
  </div>

  <div class="right-panel">
    <div class="form-header">
      <h2>Premiere connexion</h2>
      <p>Envoyez votre demande a l'administration.</p>
      <?php if(isset($_GET['sent'])): ?>
        <p style="color:#22c55e;font-family:'Sora',sans-serif;font-size:0.9rem;margin-top:10px;">Demande envoyee avec succes.</p>
      <?php elseif(isset($_GET['error'])): ?>
        <p style="color:#ff6b6b;font-family:'Sora',sans-serif;font-size:0.9rem;margin-top:10px;">Veuillez remplir tous les champs.</p>
      <?php endif; ?>
    </div>

    <div class="role-selector">
      <button class="role-btn active" type="button" onclick="setRole(this, 'Etudiant')">Etudiant</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Enseignant')">Enseignant</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Assistant')">Assistant</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Doyen')">Doyen</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Vice-Doyen')">Vice-Doyen</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Apparitaire')">Apparitaire</button>
      <button class="role-btn" type="button" onclick="setRole(this, 'Administrateur-Academique')">Admin</button>
    </div>

    <form action="index.php?action=contact_admin" method="POST">
      <input type="hidden" name="csrf_token" value="<?= h(\App\Services\SecurityService::csrfToken()) ?>">
      <input type="hidden" name="role_selectionne" id="role_selectionne" value="Etudiant">
      <div class="form-group"><label class="form-label">Nom</label><div class="input-wrapper"><span class="input-icon">👤</span><input type="text" name="nom" class="form-input" required></div></div>
      <div class="form-group"><label class="form-label">Prenom</label><div class="input-wrapper"><span class="input-icon">👤</span><input type="text" name="prenom" class="form-input" required></div></div>
      <div class="form-group"><label class="form-label">Adresse email</label><div class="input-wrapper"><span class="input-icon">@</span><input type="email" name="email" class="form-input" required></div></div>
      <div class="form-group"><label class="form-label">Message</label><textarea name="message" class="form-input" style="min-height:92px;resize:vertical;" required></textarea></div>
      <button type="submit" class="btn-login">Envoyer la demande →</button>
    </form>

    <div class="register-link"><a href="login.php">Retour a la connexion</a></div>
  </div>
</div>
<script src="assets/js/login.js"></script>
</body>
</html>
