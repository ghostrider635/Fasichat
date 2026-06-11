function showView(view, btn) {
  document.getElementById('view-students').classList.remove('visible');
  document.getElementById('view-mur').classList.remove('visible');
  document.getElementById('view-msgs').classList.remove('visible');
  document.getElementById('input-area').style.display = 'none';
  if (view === 'students') document.getElementById('view-students').classList.add('visible');
  else if (view === 'mur') document.getElementById('view-mur').classList.add('visible');
  else if (view === 'msgs') {
    document.getElementById('view-msgs').classList.add('visible');
    document.getElementById('input-area').style.display = 'block';
    setTimeout(() => { const m = document.getElementById('view-msgs'); m.scrollTop = m.scrollHeight; }, 50);
  }
  if (btn) {
    document.querySelectorAll('.nav-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }
}

function selectConv(item, title, icon, bg, sub, type) {
  document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('active'));
  item.classList.add('active');
  const receiverId = item.dataset.userId;
  if (receiverId) {
    openDashboardConversation(receiverId, item);
    return;
  }
  if (item.dataset.courseId) {
    openCourseConversation(item);
    return;
  }
  document.getElementById('topbarTitle').textContent = title;
  document.getElementById('topbarSub').textContent = sub;
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

  const data = new FormData();
  const mediaInput = document.getElementById('mediaInput');
  const file = mediaInput?.files?.[0] || null;

  const receiverId = window.FASI_ACTIVE_RECEIVER_ID || window.FASI_PAGE_DATA?.students?.[0]?.id || window.FASI_PAGE_DATA?.privateContacts?.[0]?.id || '18';

  if (!text && !file) return;

  data.append('receiver_id', receiverId);
  if (text !== '') data.append('content', text);
  if (file) data.append('file', file);


  try {
    await apiPost('message_send', data);
  } catch (error) {
    alert(error.message || "Erreur pendant l'envoi du message.");
    return;
  }

  const msgs = document.getElementById('view-msgs');
  const now = new Date();
  const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
  const row = document.createElement('div');
  row.className = 'msg-row mine';
  row.innerHTML = `<div class="msg-avatar" style="background:linear-gradient(135deg,#f59e0b,#d97706);">${escapeHtml(window.FASI_PAGE_DATA?.userInitials || 'Moi')}</div><div class="msg-group"><div class="bubble mine">${escapeHtml(text)}</div><div class="msg-meta">${time} OK</div></div>`;
  msgs.appendChild(row);
  ta.value = '';
  ta.style.height = 'auto';
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

function renderTeacherConversation(payload) {
  const box = document.getElementById('view-msgs');
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
      <div class="msg-avatar" style="background:${mine ? 'linear-gradient(135deg,#f59e0b,#d97706)' : 'linear-gradient(135deg,#3b82f6,#1d4ed8)'};">${escapeHtml(initials)}</div>
      <div class="msg-group">
        ${mine ? '' : `<div class="msg-sender">${escapeHtml(sender)} · ${escapeHtml(formatDate(message.created_at))}</div>`}
        <div class="bubble ${mine ? 'mine' : 'theirs'}">${escapeHtml(message.contenu)}</div>
        <div class="msg-meta">${escapeHtml(formatDate(message.created_at))}${mine ? ' OK' : ''}</div>
      </div>
    </div>`;
  }).join('');
  box.scrollTop = box.scrollHeight;
}

function updateTeacherConversationHeader(user) {
  const avatar = document.getElementById('topbarAvatar');
  const badge = document.querySelector('.status-badge');
  document.getElementById('topbarTitle').textContent = personName(user);
  document.getElementById('topbarSub').textContent = `${user.role_nom || 'Contact'} · ${user.email || ''}`;
  if (avatar) avatar.textContent = personInitials(user);
  if (badge) badge.innerHTML = '<div class="status-dot"></div> Message Privé';
}

function courseById(courseId) {
  return (window.FASI_PAGE_DATA?.courses || []).find(course => Number(course.id) === Number(courseId));
}

function openCourseConversation(item) {
  const courseId = item?.dataset?.courseId;
  if (!courseId) return;
  const course = courseById(courseId);
  window.FASI_ACTIVE_COURSE_ID = courseId;
  window.FASI_ACTIVE_RECEIVER_ID = null;
  document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('active'));
  item.classList.add('active');
  document.getElementById('topbarTitle').textContent = item.dataset.courseTitle || `${course?.nom || 'Cours'} - ${course?.promotion || 'Promotion'}`;
  document.getElementById('topbarSub').textContent = item.dataset.courseSubtitle || `${course?.enseignants || 'Aucun enseignant'} - ${course?.student_count || 0} etudiants`;
  const avatar = document.getElementById('topbarAvatar');
  const badge = document.querySelector('.status-badge');
  if (avatar) avatar.textContent = 'FC';
  if (badge) badge.innerHTML = '<div class="status-dot"></div>Cours Public';
}

async function openDashboardConversation(receiverId, item = null) {
  if (!receiverId) return;
  window.FASI_ACTIVE_RECEIVER_ID = receiverId;
  window.FASI_ACTIVE_COURSE_ID = null;
  showView('msgs', null);
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
  updateTeacherConversationHeader(payload.other_user);
  renderTeacherConversation(payload);
}

function initDashboardConversationPalette() {
  const contacts = window.FASI_PAGE_DATA?.privateContacts || [];
  const panel = document.querySelector('.sidebar .conv-list');
  if (!panel || !contacts.length) return;

  let inPrivateSection = false;
  let index = 0;
  Array.from(panel.children).forEach(child => {
    if (child.classList.contains('section-label')) {
      const text = child.textContent.toLowerCase();
      inPrivateSection = text.includes('priv');
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

async function publishPost() {
  const ta = document.querySelector('.mur-textarea');
  const text = ta.value.trim();
  if (!text) return;

  const data = new FormData();
  data.append('course_id', window.FASI_ACTIVE_COURSE_ID || window.FASI_PAGE_DATA?.courses?.[0]?.id || '1');
  data.append('content', text);

  try {
    await apiPost('mur_publish', data);
  } catch (error) {
    alert(error.message || 'Erreur pendant la publication.');
    return;
  }

  const posts = document.getElementById('mur-posts');
  const post = document.createElement('div');
  const course = courseById(window.FASI_ACTIVE_COURSE_ID || window.FASI_PAGE_DATA?.courses?.[0]?.id);
  post.className = 'mur-post';
  post.innerHTML = `<div class="post-header"><div class="post-avatar" style="background:linear-gradient(135deg,#f59e0b,#d97706);">${escapeHtml(window.FASI_PAGE_DATA?.userInitials || 'Moi')}</div><div><div class="post-author">${escapeHtml(personName(window.FASI_PAGE_DATA?.user || {}))}</div><div class="post-meta">A l'instant - ${escapeHtml(course?.nom || 'Cours')}</div></div><div class="post-actions"><button class="post-action-btn">...</button><button class="post-action-btn">x</button></div></div><div class="post-content">${escapeHtml(text)}</div>`;
  posts.insertBefore(post, posts.firstChild);
  ta.value = '';
}

function escapeHtml(value) {
  return String(value).replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
}

document.getElementById('msgInput').addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

window.addEventListener('load', initDashboardConversationPalette);
window.openDashboardConversation = openDashboardConversation;
window.initDashboardConversationPalette = initDashboardConversationPalette;
window.openCourseConversation = openCourseConversation;
