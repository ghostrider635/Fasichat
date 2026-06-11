<?php require __DIR__ . '/_bootstrap.php'; requireDashboardRoles(['Doyen', 'Administrateur', 'Administrateur-Academique']); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FasiChat — Dashboard Administratif</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/dashboard_admin.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <div class="sidebar-header">
    <div class="brand-mark">🏛</div>
    <div class="brand-info"><h3>FasiChat Admin</h3><span>Espace Administratif</span></div>
  </div>

  <div class="role-badge-sidebar">
    <div class="rdot"></div>
    <span>DOYEN — Accès complet</span>
  </div>

  <div id="leftMenuMount"></div>


  <div class="sidebar-bottom">
    <div class="profile-row">
      <div class="profile-ava"><div class="online-dot"></div>🏛</div>
      <div class="profile-info"><h4>Pr. KUTANGILA</h4><span>Doyen de la Faculté</span></div>
      <a href="index.php?action=logout" class="logout-btn">🚪</a>
    </div>
  </div>
</div>

<!-- MAIN AREA -->
<div class="main-area">
  <div class="admin-topbar">
    <div>
      <div class="topbar-title">Tableau de bord — Doyen</div>
      <div class="topbar-sub">Faculté des Sciences &amp; Technologies · Année 2024–2025</div>
    </div>
    <div class="topbar-right">
      <button class="tb-btn ghost" onclick="location.href='valve.php'">📣 Valve</button>
      <script>
        // si l'utilisateur n'est pas Apparitaire, on évite que l'action de publication échoue.
        // (lecture seule reste autorisée sur valve.php)
      </script>

      <button class="tb-btn primary" onclick="openConvocModal()">📅 Convoquer une réunion</button>
    </div>
  </div>

  <div class="admin-content">

    <!-- STATS -->
    <div class="stats-row">
      <div class="stat-card blue">
        <div class="stat-icon">🎓</div>
        <div class="stat-number"></div>
        <div class="stat-label">Étudiants inscrits</div>
        <div class="stat-trend up"></div>
      </div>
      <div class="stat-card gold">
        <div class="stat-icon">👨‍🏫</div>
        <div class="stat-number"></div>
        <div class="stat-label">Enseignants actifs</div>
        <div class="stat-trend neutral"></div>
      </div>
      <div class="stat-card green">
        <div class="stat-icon">📚</div>
        <div class="stat-number"></div>
        <div class="stat-label">Cours en cours</div>
        <div class="stat-trend up"></div>
      </div>
      <div class="stat-card red">
        <div class="stat-icon">📅</div>
        <div class="stat-number"></div>
        <div class="stat-label">Convocations envoyées</div>
        <div class="stat-trend neutral"></div>
      </div>
    </div>


    <!-- TWO COLUMNS -->
    <div class="two-col">

      <!-- USERS (dynamique) -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">👥 Utilisateurs (DB)</div>
          <button class="card-action">Voir tous →</button>
        </div>
        <div style="overflow-x:auto;">
          <div id="usersMount"></div>
        </div>
      </div>

      <!-- RIGHT COLUMN -->
      <div style="display:flex;flex-direction:column;gap:16px;">

        <!-- COURSES (dynamique) -->
        <div class="card">
          <div class="card-header">
            <div class="card-title">📚 Cours & Promotions (DB)</div>
          </div>
          <div id="coursesMount" class="courses-mount"></div>
        </div>

        <!-- RECENT ACTIVITY (déjà gérée par dashboard_admin_dynamic.js) -->
        <div class="card">
          <div class="card-header">
            <div class="card-title">🕐 Activité récente</div>
            <button class="card-action">Tout voir</button>
          </div>
          <div class="activity-list" id="recentActivityList"></div>
        </div>

      </div>
    </div>


  </div>
</div>

<!-- CONVOCATION MODAL -->
<div class="modal-overlay" id="convocModal" onclick="closeModalOutside(event)">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-header-icon">📅</div>
      <div>
        <h3>Convoquer une réunion</h3>
        <p>La convocation sera envoyée à tous les enseignants et assistants</p>
      </div>
      <button class="modal-close-btn" onclick="closeConvocModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Objet de la réunion *</label>
        <input type="text" class="form-input" id="convocObj" placeholder="Ex: Réunion extraordinaire du conseil pédagogique...">
      </div>
      <div class="form-row-2">
        <div class="form-group">
          <label class="form-label">Date *</label>
          <input type="date" class="form-input" id="convocDate">
        </div>
        <div class="form-group">
          <label class="form-label">Heure *</label>
          <input type="time" class="form-input" id="convocHeure">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Lieu ou lien de réunion *</label>
        <input type="text" class="form-input" id="convocLieu" placeholder="Amphi A, Salle B-04, ou https://meet.google.com/...">
      </div>
      <div class="form-group">
        <label class="form-label">Message complémentaire</label>
        <textarea class="form-textarea" id="convocMsg" placeholder="Précisez l'ordre du jour, les documents à préparer..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Destinataires</label>
        <div class="recipients-box">
          <div class="recipient-tag">👨‍🏫 Tous les enseignants (24)</div>
          <div class="recipient-tag">📋 Tous les assistants (6)</div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeConvocModal()">Annuler</button>
      <button class="btn-send" onclick="sendConvocModal()">📨 Envoyer la convocation</button>
    </div>
  </div>
</div>

<script src="assets/js/dashboard_admin.js"></script>
<script src="assets/js/dashboard_admin_dynamic.js"></script>
<script src="assets/js/dashboard_doyen_leftmenu_dynamic.js"></script>




<script>
window.FASI_PAGE_DATA = <?= json_encode(pageData(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
function openAdminPrivateMessages(item){
  if(item)setNav(item);
  const contact=(window.FASI_PAGE_DATA?.privateContacts||[]).find(user=>String(user.role_nom||'').includes('Vice-Doyen'));
  if(!contact){alert('Contact Vice-Doyen introuvable.');return;}
  location.href=`conversation.php?with=${encodeURIComponent(contact.id)}`;
}
</script>
<script src="assets/js/dynamic-db.js"></script>

</body>
</html>
