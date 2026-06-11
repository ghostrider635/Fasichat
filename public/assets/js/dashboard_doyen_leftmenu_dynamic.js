(function () {
  const pageData = window.FASI_PAGE_DATA || {};

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

  function mountLeftMenu(menuRoot, role) {
    if (!menuRoot) return;

    // Menu reconstruit côté JS (items + libellés + badges si fournis)
    const menu = {
      Administration: [
        { key: 'dashboard', label: 'Tableau de bord', sub: 'Vue d\'ensemble', icon: '📊', href: 'dashboard_admin.php' },
        { key: 'convocation', label: 'Convoquer réunion', sub: role === 'Vice-Doyen' ? 'Commission de recherche' : 'Enseignants & assistants', icon: '📅', href: 'dashboard_admin.php' },
        { key: 'users', label: 'Utilisateurs', sub: 'Gérer & consulter', icon: '👥', badge: null, href: 'dashboard_admin.php' },
        { key: 'courses', label: 'Cours & Promotions', sub: 'Organiser les formations', icon: '📚', badge: null, href: 'dashboard_admin.php' }
      ],
      Communication: [
        { key: 'valve', label: 'Valve', sub: 'Tableau d\'affichage', icon: '📣', href: 'valve.php' },
        { key: 'messages', label: 'Messages privés', sub: 'Doyen ↔ Vice-Doyen', icon: '💬', badge: 2, href: null }
      ],
      Navigation: [
        { key: 'student', label: 'Vue Étudiant', sub: 'Dashboard étudiant', icon: '🎓', href: 'dashboard_etudiant.php' },
        { key: 'teacher', label: 'Vue Enseignant', sub: 'Dashboard enseignant', icon: '👨‍🏫', href: 'dashboard_enseignant.php' }
      ]
    };

    menuRoot.innerHTML = Object.entries(menu).map(([sectionLabel, items]) => {
      const itemsHtml = items.map((it) => {
        const badgeHtml = it.badge !== null && it.badge !== undefined ? `<div class="nav-badge">${escapeHtml(it.badge)}</div>` : '';
        const onclick = it.key === 'messages' ? 'openAdminPrivateMessages(this)' : 'setNav(this)';
        return `
          <div class="nav-item" data-nav-key="${escapeHtml(it.key)}" onclick="${onclick}" data-href="${escapeHtml(it.href || '')}">
            <div class="nav-icon" style="background:rgba(124,58,237,0.12);">${escapeHtml(it.icon)}</div>
            <div>
              <div class="nav-label">${escapeHtml(it.label)}</div>
              <div class="nav-sub">${escapeHtml(it.sub)}</div>
            </div>
            ${badgeHtml}
          </div>
        `;
      }).join('');

      return `
        <div class="nav-section">
          <div class="nav-section-label">${escapeHtml(sectionLabel)}</div>
          ${itemsHtml}
        </div>
      `;
    }).join('');

    // fallback : navigation
    menuRoot.querySelectorAll('.nav-item').forEach(item => {
      item.addEventListener('click', () => {
        const key = item.dataset.navKey;
        if (key === 'messages') return;
        const href = item.getAttribute('data-href');
        if (href) window.location.href = href;
      });
    });
  }

  function renderCoursesToSection(courses, root) {
    if (!root) return;
    root.innerHTML = '';
    if (!Array.isArray(courses) || !courses.length) return;

    const html = courses.slice(0, 8).map((c) => {
      const promoText = c.promotion ? c.promotion : 'Aucune promotion';
      return `
        <div class="conv-item" data-course-id="${escapeHtml(c.id)}">
          <div class="avatar avatar-group" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">FC</div>
          <div class="conv-info">
            <div class="conv-name">${escapeHtml(c.nom)}</div>
            <div class="conv-preview">${escapeHtml(promoText)}</div>
          </div>
          <div class="conv-meta">
            <div class="conv-time">${escapeHtml(c.code || '')}</div>
          </div>
        </div>
      `;
    }).join('');

    root.innerHTML = html;
  }

  function renderUsersToSection(users, root) {
    if (!root) return;
    root.innerHTML = '';
    if (!Array.isArray(users) || !users.length) return;

    const html = users.slice(0, 8).map((u) => {
      const initials = `${(u.prenom || '').slice(0, 1)}${(u.nom || '').slice(0, 1)}`.toUpperCase();
      const name = `${u.prenom || ''} ${u.nom || ''}`.trim() || u.email;
      return `
        <div class="user-mini">
          <div class="user-row-ava">${escapeHtml(initials)}</div>
          <div>
            <div class="user-name">${escapeHtml(name)}</div>
            <div class="user-email">${escapeHtml(u.role_nom || '')}</div>
          </div>
        </div>
      `;
    }).join('');

    root.innerHTML = html;
  }

  async function init() {
    const role = pageData?.role || pageData?.user?.role_nom || '';

    const menuRoot = document.getElementById('leftMenuMount');
    const coursesBox = document.getElementById('coursesMount');
    const usersBox = document.getElementById('usersMount');

    mountLeftMenu(menuRoot, role);

    try {
      const [coursesRes, usersRes] = await Promise.all([
        apiGet('dashboard_doyen_courses_list'),
        apiGet('dashboard_doyen_users_list')
      ]);

      if (coursesRes?.success) renderCoursesToSection(coursesRes.courses || [], coursesBox);
      if (usersRes?.success) renderUsersToSection(usersRes.users || [], usersBox);
    } catch (e) {
      console.error(e);
    }
  }

  window.addEventListener('load', init);
})();

