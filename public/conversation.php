<?php
require __DIR__ . '/_bootstrap.php';
requireAuthPage();

$currentUser = currentUser();
$currentUserId = (int)($currentUser['id'] ?? 0);
$conversationRepository = new \App\Repositories\ConversationRepository();
$messageRepository = new \App\Repositories\MessageRepository();

$withId = (int)($_GET['with'] ?? 0);
$conversationId = (int)($_GET['id'] ?? 0);

if ($withId > 0) {
    $otherUser = fetchOneSafe(
        'SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role_nom
         FROM utilisateurs u
         JOIN roles r ON r.id = u.role_id
         WHERE u.id = :id AND u.id <> :current_id',
        ['id' => $withId, 'current_id' => $currentUserId]
    );

    if (!$otherUser) {
        header('Location: ' . dashboardForRole($currentUser['role_nom'] ?? ''));
        exit();
    }

    $conversationId = $conversationRepository->getOrCreatePrivateConversation($currentUserId, $withId);
} elseif ($conversationId > 0) {
    if (!$conversationRepository->userCanAccessConversation($conversationId, $currentUserId)) {
        header('Location: ' . dashboardForRole($currentUser['role_nom'] ?? ''));
        exit();
    }

    $conversation = $conversationRepository->findById($conversationId);
    $withId = (int)(($conversation['expediteur_id'] ?? 0) === $currentUserId ? $conversation['destinataire_id'] : $conversation['expediteur_id']);
    $otherUser = fetchOneSafe(
        'SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role_nom
         FROM utilisateurs u
         JOIN roles r ON r.id = u.role_id
         WHERE u.id = :id',
        ['id' => $withId]
    );
} else {
    $firstContact = fetchOneSafe(
        'SELECT u.id FROM utilisateurs u WHERE u.id <> :id ORDER BY u.nom, u.prenom LIMIT 1',
        ['id' => $currentUserId]
    );
    if ($firstContact) {
        header('Location: conversation.php?with=' . (int)$firstContact['id']);
        exit();
    }

    header('Location: ' . dashboardForRole($currentUser['role_nom'] ?? ''));
    exit();
}

$messages = $messageRepository->fetchConversationMessages($conversationId);
$existingConversations = $conversationRepository->listPrivateForUser($currentUserId);
$contacts = fetchAllSafe(
    'SELECT u.id AS user_id, u.nom, u.prenom, u.email, r.nom AS role_nom
     FROM utilisateurs u
     JOIN roles r ON r.id = u.role_id
     WHERE u.id <> :id
     ORDER BY
       CASE
         WHEN r.nom IN ("Enseignant", "Assistant") THEN 1
         WHEN r.nom = "Etudiant" THEN 2
         ELSE 3
       END,
       u.nom,
       u.prenom
     LIMIT 20',
    ['id' => $currentUserId]
);

$conversationItems = [];
foreach ($existingConversations as $item) {
    $conversationItems[(int)$item['user_id']] = $item;
}
foreach ($contacts as $contact) {
    $contactId = (int)$contact['user_id'];
    if (!isset($conversationItems[$contactId])) {
        $conversationItems[$contactId] = $contact + [
            'conversation_id' => null,
            'last_message' => 'Ouvrir la discussion',
            'last_message_at' => null,
        ];
    }
}

$otherName = trim(($otherUser['prenom'] ?? '') . ' ' . ($otherUser['nom'] ?? ''));
$otherInitials = initials($otherUser);
$currentInitials = initials($currentUser);
$backUrl = dashboardForRole($currentUser['role_nom'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FasiChat - Discussion avec <?= h($otherName) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/etudiant.css">
</head>
<body>

<div class="sidebar">
  <div class="sidebar-header">
    <div class="brand-mark">💬</div>
    <div class="brand-info"><h3>FasiChat</h3><span>Discussions privees</span></div>
  </div>

  <div class="nav-tabs">
    <button class="nav-tab active" onclick="location.href='conversation.php?with=<?= (int)$withId ?>'">💬 Messages</button>
    <button class="nav-tab" onclick="location.href='<?= h($backUrl) ?>'">↩ Dashboard</button>
    <button class="nav-tab" onclick="location.href='valve.php'">📣 Valve</button>
  </div>

  <div class="sidebar-search">
    <div class="search-wrap">
      <span class="search-icon">🔍</span>
      <input type="text" class="search-input" placeholder="Rechercher..." id="conversationSearch">
    </div>
  </div>

  <div class="conv-list" id="conversationList">
    <div class="section-label">Discussions</div>
    <?php foreach ($conversationItems as $item): ?>
      <?php
        $itemUserId = (int)$item['user_id'];
        $itemName = trim(($item['prenom'] ?? '') . ' ' . ($item['nom'] ?? ''));
        $activeClass = $itemUserId === (int)$withId ? ' active' : '';
        $preview = $item['last_message'] ?: 'Ouvrir la discussion';
      ?>
      <div class="conv-item<?= $activeClass ?>" data-name="<?= h(mb_strtolower($itemName . ' ' . ($item['email'] ?? ''))) ?>" onclick="location.href='conversation.php?with=<?= $itemUserId ?>'">
        <div class="avatar avatar-blue" style="font-size:13px;font-weight:700;"><?= h(initials($item)) ?></div>
        <div class="conv-info">
          <div class="conv-name"><?= h($itemName) ?></div>
          <div class="conv-preview"><?= h(mb_substr($preview, 0, 70)) ?></div>
        </div>
        <div class="conv-meta">
          <div class="conv-time"><?= h($item['last_message_at'] ? formatDateTime($item['last_message_at']) : $item['role_nom']) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="sidebar-profile">
    <div class="profile-avatar"><div class="online-dot"></div><?= h($currentInitials) ?></div>
    <div class="profile-info"><h4><?= h(trim(($currentUser['prenom'] ?? '') . ' ' . ($currentUser['nom'] ?? ''))) ?></h4><span><?= h($currentUser['role_nom'] ?? '') ?></span></div>
    <div class="profile-actions"><a href="index.php?action=logout" class="icon-btn" title="Deconnexion">🚪</a></div>
  </div>
</div>

<div class="main-area">
  <div class="chat-topbar">
    <div class="chat-topbar-avatar"><?= h($otherInitials) ?></div>
    <div class="chat-topbar-info">
      <h3><?= h($otherName) ?></h3>
      <p><?= h($otherUser['role_nom'] ?? '') ?> · <?= h($otherUser['email'] ?? '') ?></p>
    </div>
    <div class="status-badge public">
      <div class="status-dot"></div>
      Message prive
    </div>
    <div class="topbar-actions">
      <button class="topbar-btn" onclick="location.href='<?= h($backUrl) ?>'">↩ Retour</button>
    </div>
  </div>

  <div class="chat-messages" id="messages">
    <div class="date-sep">Discussion privee</div>
    <?php if (empty($messages)): ?>
      <div class="msg-row">
        <div class="msg-avatar"><?= h($otherInitials) ?></div>
        <div class="msg-group">
          <div class="bubble theirs">Aucun message pour le moment. Envoyez le premier message.</div>
          <div class="msg-meta">Maintenant</div>
        </div>
      </div>
    <?php endif; ?>

    <?php foreach ($messages as $message): ?>
      <?php $mine = (int)$message['expediteur_id'] === $currentUserId; ?>
      <div class="msg-row<?= $mine ? ' mine' : '' ?>">
        <div class="msg-avatar" style="background:<?= $mine ? 'linear-gradient(135deg,var(--sky),var(--accent))' : 'linear-gradient(135deg,#3b82f6,#1d4ed8)' ?>;">
          <?= h($mine ? $currentInitials : initials($message)) ?>
        </div>
        <div class="msg-group">
          <?php if (!$mine): ?>
            <div class="msg-sender"><?= h(trim(($message['prenom'] ?? '') . ' ' . ($message['nom'] ?? ''))) ?> · <?= h(formatDateTime($message['created_at'] ?? '')) ?></div>
          <?php endif; ?>
          <div class="bubble <?= $mine ? 'mine' : 'theirs' ?>"><?= h($message['contenu'] ?? '') ?></div>
          <div class="msg-meta"><?= h(formatDateTime($message['created_at'] ?? '')) ?><?= $mine ? ' OK' : '' ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="chat-input-area">
    <div class="input-toolbar">
      <button class="toolbar-btn" type="button" id="attachFile">📎 Fichier</button>
      <button class="toolbar-btn" type="button" id="attachImage">🖼 Image</button>
      <button class="toolbar-btn" type="button" id="attachPdf">📊 PDF</button>
    </div>
    <div class="input-row">
      <button class="voice-btn" title="Message vocal" type="button">🎤</button>
      <div class="msg-textarea-wrap">
        <textarea class="msg-textarea" placeholder="Ecrire un message prive..." rows="1" id="msgInput" onkeydown="handleConversationKey(event)"></textarea>
        <button class="emoji-btn" type="button">😊</button>
      </div>
      <button class="send-btn" onclick="sendConversationMessage()" type="button">➤</button>
    </div>
  </div>
</div>

<div class="right-panel">
  <div class="panel-section">
    <div class="panel-title">Contact</div>
    <div class="info-card">
      <h4><?= h($otherName) ?></h4>
      <p><?= h($otherUser['role_nom'] ?? '') ?></p>
      <div class="tag-row">
        <span class="tag tag-blue"><?= h($otherUser['email'] ?? '') ?></span>
      </div>
    </div>
  </div>
  <div class="panel-section">
    <div class="panel-title">Conversation</div>
    <div class="info-card">
      <h4><?= count($messages) ?></h4>
      <p>messages dans cette discussion privee</p>
    </div>
  </div>
</div>

<script>
window.FASI_CONVERSATION = {
  receiverId: <?= (int)$withId ?>,
  currentInitials: <?= json_encode($currentInitials, JSON_UNESCAPED_UNICODE) ?>,
  csrfToken: <?= json_encode(\App\Services\SecurityService::csrfToken(), JSON_UNESCAPED_UNICODE) ?>
};

function escapeHtml(value) {
  return String(value).replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
}

function handleConversationKey(event) {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault();
    sendConversationMessage();
  }
}

async function sendConversationMessage() {
  const input = document.getElementById('msgInput');
  const text = input.value.trim();
  if (!text) return;

  const form = new FormData();
  form.append('receiver_id', window.FASI_CONVERSATION.receiverId);
  form.append('content', text);
  form.append('csrf_token', window.FASI_CONVERSATION.csrfToken);

  const response = await fetch('index.php?action=message_send', {
    method: 'POST',
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: form
  });
  if (!response.ok) {
    alert(await response.text());
    return;
  }

  const now = new Date();
  const time = now.toLocaleString('fr-FR');
  const row = document.createElement('div');
  row.className = 'msg-row mine';
  row.innerHTML = `<div class="msg-avatar" style="background:linear-gradient(135deg,var(--sky),var(--accent));">${escapeHtml(window.FASI_CONVERSATION.currentInitials)}</div><div class="msg-group"><div class="bubble mine">${escapeHtml(text)}</div><div class="msg-meta">${escapeHtml(time)} OK</div></div>`;
  const box = document.getElementById('messages');
  box.appendChild(row);
  input.value = '';
  input.style.height = 'auto';
  box.scrollTop = box.scrollHeight;
}

function initSearch() {
  const input = document.getElementById('conversationSearch');
  input?.addEventListener('input', () => {
    const normalize = value => String(value ?? '')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .trim();
    const query = normalize(input.value);
    document.querySelectorAll('#conversationList .conv-item').forEach(item => {
      const text = normalize(`${item.dataset.name || ''} ${item.textContent || ''}`);
      item.style.display = query === '' || text.includes(query) ? '' : 'none';
    });
  });
}

function initAutoResize() {
  const input = document.getElementById('msgInput');
  input.addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
  });
}

window.addEventListener('load', () => {
  const box = document.getElementById('messages');
  box.scrollTop = box.scrollHeight;
  initSearch();
  initAutoResize();
});
</script>
</body>
</html>
