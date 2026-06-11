(function () {
  const data = window.FASI_PAGE_DATA || {};

  function safeText(v) {
    return String(v ?? '');
  }

  function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (c) => ({
      '&': '&amp;',
      '<': '<',
      '>': '>',
      '"': '"',
      "'": '&#039;'
    }[c]));
  }

  async function apiGet(action) {
    const response = await fetch(`index.php?action=${action}`, {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (!response.ok) throw new Error(await response.text());
    return response.json();
  }

  function renderStats(stats) {
    // dashboard_admin.php a les class .stat-number sur 4 cartes
    const cards = document.querySelectorAll('.stats-row .stat-card');
    if (!cards.length || !stats) return;

    const nums = cards.length ? cards : [];

    const usersEl = document.querySelector('.stats-row .stat-card.blue .stat-number');
    const teachersEl = document.querySelector('.stats-row .stat-card.gold .stat-number');
    const coursesEl = document.querySelector('.stats-row .stat-card.green .stat-number');
    const convEl = document.querySelector('.stats-row .stat-card.red .stat-number');

    if (usersEl) usersEl.textContent = stats.users_total ?? '';
    if (teachersEl) teachersEl.textContent = stats.teachers_total ?? '';
    if (coursesEl) coursesEl.textContent = stats.courses_total ?? '';
    if (convEl) convEl.textContent = stats.convocations_total ?? '';
  }

  function renderRecentUsers(users) {
    const tbody = document.getElementById('recentUsersTbody') || document.querySelector('.users-table tbody');

    if (!tbody) return;
    if (!Array.isArray(users) || users.length === 0) return;

    tbody.innerHTML = users.map((u) => {
      const initials = safeText((u.prenom || '').slice(0, 1) + (u.nom || '').slice(0, 1)).toUpperCase();
      const role = safeText(u.role_nom || '');
      return `
        <tr>
          <td>
            <div class="user-cell">
              <div class="user-row-ava" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">${escapeHtml(initials)}</div>
              <div>
                <div class="user-name">${escapeHtml(`${u.prenom || ''} ${u.nom || ''}`.trim() || u.email)}</div>
                <div class="user-email">${escapeHtml(u.email || '')}</div>
              </div>
            </div>
          </td>
          <td><span class="role-pill">${escapeHtml(role)}</span></td>
          <td>Actif</td>
          <td><div class="action-btns"><button class="act-btn edit">✏</button></div></td>
        </tr>
      `;
    }).join('');
  }

  function renderActivity(items) {
    const list = document.getElementById('recentActivityList') || document.querySelector('.activity-list');

    if (!list) return;
    if (!Array.isArray(items) || items.length === 0) return;

    list.innerHTML = items.map((it) => {
      const type = it.type === 'convocation' ? 'Convocation envoyée' : 'Annonce Valve publiée';
      const subtitle = safeText(it.subtitle || it.title || '');
      return `
        <div class="activity-item">
          <div class="act-icon-wrap" style="background:rgba(99,102,241,0.1);">${type.includes('Convocation') ? '📅' : '📣'}</div>
          <div class="act-text"><strong>${escapeHtml(type)}</strong><p>${escapeHtml(subtitle)}</p></div>
          <div class="act-time">${escapeHtml(it.created_at ? new Date(String(it.created_at).replace(' ', 'T')).toLocaleString('fr-FR') : '')}</div>
        </div>
      `;
    }).join('');
  }

  async function init() {
    try {
      const [statsRes, usersRes, actRes] = await Promise.all([
        apiGet('dashboard_doyen_stats'),
        apiGet('dashboard_doyen_recent_users'),
        apiGet('dashboard_doyen_recent_activity')
      ]);

      renderStats(statsRes?.stats || {});
      renderRecentUsers(usersRes?.users || []);
      renderActivity(actRes?.items || []);
    } catch (e) {
      // silently ignore to avoid breaking UI
      console.error(e);
    }
  }

  window.addEventListener('load', init);
})();

