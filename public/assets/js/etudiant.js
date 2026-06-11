function switchTab(btn, tab) {
  document.querySelectorAll('.nav-tab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

function selectConv(item) {
  document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('active'));
  item.classList.add('active');
  const receiverId = item.dataset.userId;
  if (receiverId) {
    openDashboardConversation(receiverId, item);
    return;
  }
  if (item.dataset.courseId) {
    openCourseConversation(item);
  }
}

function handleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMsg();
  }
}

async function apiPost(action, data) {
  if (data instanceof FormData && window.FASI_PAGE_DATA?.csrfToken) {
    data.set('csrf_token', window.FASI_PAGE_DATA.csrfToken);
  }
  const response = await fetch(`index.php?action=${action}`, {
    method: 'POST',
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: data
  });
  if (!response.ok) throw new Error(await response.text());
  return response.json();
}

async function sendMsg() {
  const ta = document.getElementById('msgInput');
  const text = ta.value.trim();

  // Input media (optionnel)
  const data = new FormData();
  const action = window.FASI_ACTIVE_COURSE_ID ? 'mur_publish' : 'message_send';

  // Envoi media uniquement supporté pour message privé (message_send)
  // (mur_publish n'est pas encore étendu pour les pièces jointes)



  // Input media (optionnel) - utilisé seulement quand action === message_send
  const mediaInput = document.getElementById('mediaInput');
  const file = mediaInput?.files?.[0] || null;

  if (action === 'message_send') {
    if (!text && !file) return;

    data.append('content', text);
    if (file) data.append('file', file);
    data.append('receiver_id', window.FASI_ACTIVE_RECEIVER_ID || window.FASI_PAGE_DATA?.privateContacts?.[0]?.id || '17');
  } else {
    // mur_publish: texte uniquement pour le moment
    if (!text) return;
    data.append('content', text);
    data.append('course_id', window.FASI_ACTIVE_COURSE_ID);
  }

  try {
    await apiPost(action, data);
  } catch (error) {
    alert(error.message || "Erreur pendant l'envoi du message.");
    return;
  }

  const msgs = document.getElementById('messages');
  const now = new Date();
  const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

  const row = document.createElement('div');
  row.className = 'msg-row mine';
  if (file && action === 'message_send') {
    const mime = file.type || '';
    const bubbleHtml = mime.startsWith('image/')
      ? `<img src="${URL.createObjectURL(file)}" alt="image" style="max-width:260px;border-radius:12px;display:block;" />`
      : (mime.startsWith('video/')
        ? `<video controls style="max-width:260px;border-radius:12px;"><source src="${URL.createObjectURL(file)}" /></video>`
        : (mime.startsWith('audio/')
          ? `<audio controls src="${URL.createObjectURL(file)}"></audio>`
          : `<div style="font-weight:700;">📎 ${escapeHtml(file.name || 'media')}</div>`
        ));

    row.innerHTML = `
      <div class="msg-avatar" style="background:linear-gradient(135deg,var(--sky),var(--accent));">${escapeHtml(window.FASI_PAGE_DATA?.userInitials || 'Moi')}</div>
      <div class="msg-group">
        <div class="bubble mine">${bubbleHtml}</div>
        <div class="msg-meta">${time} OK</div>
      </div>`;
  } else {
    row.innerHTML = `
      <div class="msg-avatar" style="background:linear-gradient(135deg,var(--sky),var(--accent));">${escapeHtml(window.FASI_PAGE_DATA?.userInitials || 'Moi')}</div>
      <div class="msg-group">
        <div class="bubble mine">${escapeHtml(text)}</div>
        <div class="msg-meta">${time} <span class="check-read">OK</span></div>
      </div>`;
  }

  msgs.appendChild(row);
  ta.value = '';
  ta.style.height = 'auto';
  if (mediaInput) mediaInput.value = '';
  msgs.scrollTop = msgs.scrollHeight;
}


function personName(person) {
  return `${person?.prenom || ''} ${person?.nom || ''}`.trim() || person?.email || 'Discussion';
}

function personInitials(person) {
  const first = person?.prenom?.[0] || '';
  const last = person?.nom?.[0] || '';
  return `${first}${last}`.toUpperCase() || 'FC';
}

function formatDate(value) {
  return value ? new Date(String(value).replace(' ', 'T')).toLocaleString('fr-FR') : '';
}

function renderConversationMessages(payload) {
  const box = document.getElementById('messages');
  const messages = Array.isArray(payload.messages) ? payload.messages : [];
  if (!messages.length) {
    box.innerHTML = `<div class="date-sep">Discussion privée</div>
      <div class="msg-row">
        <div class="msg-avatar">${escapeHtml(personInitials(payload.other_user))}</div>
        <div class="msg-group"><div class="bubble theirs">Aucun message pour le moment. Envoyez le premier message.</div><div class="msg-meta">Maintenant</div></div>
      </div>`;
    return;
  }

  box.innerHTML = `<div class="date-sep">Discussion privée</div>` + messages.map(message => {
    const mine = Number(message.expediteur_id) === Number(payload.current_user_id);
    const initials = mine ? (window.FASI_PAGE_DATA?.userInitials || 'Moi') : personInitials(message);
    const sender = `${message.prenom || ''} ${message.nom || ''}`.trim();
    return `<div class="msg-row${mine ? ' mine' : ''}">
      <div class="msg-avatar" style="background:${mine ? 'linear-gradient(135deg,var(--sky),var(--accent))' : 'linear-gradient(135deg,#3b82f6,#1d4ed8)'};">${escapeHtml(initials)}</div>
      <div class="msg-group">
        ${mine ? '' : `<div class="msg-sender">${escapeHtml(sender)} · ${escapeHtml(formatDate(message.created_at))}</div>`}
        <div class="bubble ${mine ? 'mine' : 'theirs'}">${escapeHtml(message.contenu)}</div>
        <div class="msg-meta">${escapeHtml(formatDate(message.created_at))}${mine ? ' <span class="check-read">OK</span>' : ''}</div>
      </div>
    </div>`;
  }).join('');
  box.scrollTop = box.scrollHeight;
}

function updateConversationHeader(user) {
  const title = document.querySelector('.chat-topbar-info h3');
  const subtitle = document.querySelector('.chat-topbar-info p');
  const avatar = document.querySelector('.chat-topbar-avatar');
  const badge = document.querySelector('.status-badge');
  if (title) title.textContent = personName(user);
  if (subtitle) subtitle.textContent = `${user.role_nom || 'Contact'} · ${user.email || ''}`;
  if (avatar) avatar.textContent = personInitials(user);
  if (badge) badge.innerHTML = '<div class="status-dot"></div> Message Privé';
}

function courseById(courseId) {
  return (window.FASI_PAGE_DATA?.courses || []).find(course => Number(course.id) === Number(courseId));
}

function updateCourseHeader(course, item = null) {
  const title = document.querySelector('.chat-topbar-info h3');
  const subtitle = document.querySelector('.chat-topbar-info p');
  const avatar = document.querySelector('.chat-topbar-avatar');
  const badge = document.querySelector('.status-badge');
  if (title) title.textContent = item?.dataset.courseTitle || `${course?.nom || 'Cours'} - ${course?.promotion || 'Promotion'}`;
  if (subtitle) subtitle.textContent = item?.dataset.courseSubtitle || `${course?.enseignants || 'Aucun enseignant'} - ${course?.student_count || 0} etudiants`;
  if (avatar) avatar.textContent = 'FC';
  if (badge) badge.innerHTML = '<div class="status-dot"></div> Message Public';
}

function renderCourseMessages(courseId) {
  const box = document.getElementById('messages');
  const posts = (window.FASI_PAGE_DATA?.murPosts || []).filter(post => Number(post.course_id) === Number(courseId));
  if (!posts.length) {
    box.innerHTML = `<div class="date-sep">Cours public</div>
      <div class="msg-row">
        <div class="msg-avatar">FC</div>
        <div class="msg-group"><div class="bubble theirs">Aucun message public pour ce cours. Lancez la discussion.</div><div class="msg-meta">Maintenant</div></div>
      </div>`;
    return;
  }

  box.innerHTML = `<div class="date-sep">Cours public</div>` + posts.map(post => {
    const mine = Number(post.auteur_id) === Number(window.FASI_PAGE_DATA?.user?.id);
    const initials = mine ? (window.FASI_PAGE_DATA?.userInitials || 'Moi') : personInitials(post);
    const sender = `${post.prenom || ''} ${post.nom || ''}`.trim();
    return `<div class="msg-row${mine ? ' mine' : ''}">
      <div class="msg-avatar" style="background:${mine ? 'linear-gradient(135deg,var(--sky),var(--accent))' : 'linear-gradient(135deg,#3b82f6,#1d4ed8)'};">${escapeHtml(initials)}</div>
      <div class="msg-group">
        ${mine ? '' : `<div class="msg-sender">${escapeHtml(sender)} Â· ${escapeHtml(formatDate(post.created_at))}</div>`}
        <div class="bubble ${mine ? 'mine' : 'theirs'}">${escapeHtml(post.contenu)}</div>
        <div class="msg-meta">${escapeHtml(formatDate(post.created_at))}${mine ? ' <span class="check-read">OK</span>' : ''}</div>
      </div>
    </div>`;
  }).join('');
  box.scrollTop = box.scrollHeight;
}

function openCourseConversation(item) {
  const courseId = item?.dataset?.courseId;
  if (!courseId) return;
  window.FASI_ACTIVE_COURSE_ID = courseId;
  window.FASI_ACTIVE_RECEIVER_ID = null;
  document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('active'));
  item.classList.add('active');
  const course = courseById(courseId);
  updateCourseHeader(course, item);
  renderCourseMessages(courseId);
}

async function openDashboardConversation(receiverId, item = null) {
  if (!receiverId) return;
  window.FASI_ACTIVE_RECEIVER_ID = receiverId;
  window.FASI_ACTIVE_COURSE_ID = null;
  if (item) {
    document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('active'));
    item.classList.add('active');
  }

  const response = await fetch(`index.php?action=conversation_thread&with=${encodeURIComponent(receiverId)}`, {
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  });
  if (!response.ok) {
    alert(await response.text());
    return;
  }

  const payload = await response.json();
  updateConversationHeader(payload.other_user);
  renderConversationMessages(payload);
}

function initDashboardConversationPalette() {
  const contacts = window.FASI_PAGE_DATA?.privateContacts || [];
  if (contacts[0] && !window.FASI_ACTIVE_RECEIVER_ID && !window.FASI_ACTIVE_COURSE_ID) {
    window.FASI_ACTIVE_RECEIVER_ID = contacts[0].id;
  }

  const panel = document.getElementById('msgs-panel');
  if (!panel || !contacts.length) return;

  let inPrivateSection = false;
  let index = 0;
  Array.from(panel.children).forEach(child => {
    if (child.classList.contains('section-label')) {
      const text = child.textContent.toLowerCase();
      inPrivateSection = text.includes('priv') || text.includes('enseignant');
      return;
    }

    if (!child.classList.contains('conv-item') || !inPrivateSection || child.dataset.userId) return;
    const contact = contacts[index] || contacts[contacts.length - 1];
    index += 1;
    if (!contact) return;
    child.dataset.userId = contact.id;
    child.querySelector('.conv-name') && (child.querySelector('.conv-name').textContent = personName(contact));
    child.querySelector('.conv-preview') && (child.querySelector('.conv-preview').textContent = contact.role_nom || 'Contact privé');
    child.querySelector('.avatar') && (child.querySelector('.avatar').textContent = personInitials(contact));
    child.querySelector('.conv-time') && (child.querySelector('.conv-time').textContent = 'Privé');
    child.onclick = () => openDashboardConversation(contact.id, child);
  });
}

function escapeHtml(value) {
  return String(value).replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
}

document.getElementById('msgInput').addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

window.addEventListener('load', () => {
  const msgs = document.getElementById('messages');
  msgs.scrollTop = msgs.scrollHeight;
  initDashboardConversationPalette();
});

window.openDashboardConversation = openDashboardConversation;
window.initDashboardConversationPalette = initDashboardConversationPalette;
window.openCourseConversation = openCourseConversation;
