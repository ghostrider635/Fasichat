(function () {
  const data = window.FASI_PAGE_DATA || {};
  const esc = value => String(value ?? '').replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
  const initials = item => `${item?.prenom?.[0] || ''}${item?.nom?.[0] || ''}`.toUpperCase() || 'FC';
  const dateText = value => value ? new Date(String(value).replace(' ', 'T')).toLocaleString('fr-FR') : '';

  function updateProfile() {
    if (!data.user) return;
    document.querySelectorAll('.profile-info h4').forEach(el => {
      el.textContent = `${data.user.prenom || ''} ${data.user.nom || ''}`.trim();
    });
    document.querySelectorAll('.profile-info span').forEach(el => {
      el.textContent = data.user.role_nom || el.textContent;
    });
    document.querySelectorAll('.profile-ava, .profile-avatar, .ac-author-ava').forEach((el, index) => {
      if (index === 0) el.textContent = data.userInitials || initials(data.user);
    });
  }

  function updateStats() {
    const statNumbers = document.querySelectorAll('.stat-number');
    const stats = data.stats || {};
    const values = [stats.users, stats.teachers, stats.courses || stats.promotions, stats.convocations || stats.annonces].filter(v => v !== undefined);
    statNumbers.forEach((el, index) => {
      if (values[index] !== undefined) el.textContent = values[index];
    });

    const statBoxes = document.querySelectorAll('.stat-box .num');
    const boxValues = [stats.students, stats.teachers, stats.messages, stats.courses].filter(v => v !== undefined);
    statBoxes.forEach((el, index) => {
      if (boxValues[index] !== undefined) el.textContent = boxValues[index];
    });

    document.querySelectorAll('.hero-stat .n').forEach((el, index) => {
      const heroValues = [stats.annonces, 0, stats.convocations];
      if (heroValues[index] !== undefined) el.textContent = heroValues[index];
    });
  }

  function renderAdminUsers() {
    const tbody = document.querySelector('.users-table tbody');
    if (!tbody || !Array.isArray(data.users) || !data.users.length) return;
    tbody.innerHTML = data.users.map(user => `<tr>
      <td><div class="user-cell"><div class="user-row-ava" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">${esc(initials(user))}</div><div><div class="user-name">${esc(`${user.prenom || ''} ${user.nom || ''}`.trim())}</div><div class="user-email">${esc(user.email)}</div></div></div></td>
      <td><span class="role-chip role-admin">${esc(user.role_nom)}</span></td>
      <td>Actif</td>
      <td>Aujourd'hui</td>
    </tr>`).join('');
  }

  function renderAnnonceList() {
    const list = document.getElementById('annoncesList');
    if (!list || !Array.isArray(data.annonces) || !data.annonces.length) return;
    list.innerHTML = data.annonces.map(annonce => `<div class="annonce-item" data-id="${esc(annonce.id)}">
      <div class="ai-cat" style="background:rgba(99,102,241,0.1);">📢</div>
      <div class="ai-body"><div class="ai-cat-tag" style="color:var(--indigo);">${esc((annonce.categorie || 'Information').toUpperCase())}</div><div class="ai-title">${esc(annonce.titre)}</div><div class="ai-preview">${esc(annonce.contenu).substring(0, 110)}...</div><div class="ai-meta"><span class="status-pill active-pill">● Actif</span><span>${esc(dateText(annonce.created_at))}</span><span>${esc(`${annonce.prenom || ''} ${annonce.nom || ''}`.trim())}</span></div></div>
      <div class="ai-actions"><button class="ai-btn edit">✏</button><button class="ai-btn del" onclick="deleteAnnonce(this)">🗑</button></div>
    </div>`).join('');
  }

  function renderStudents() {
    const grid = document.querySelector('.students-grid');
    if (!grid || !Array.isArray(data.students) || !data.students.length) return;
    grid.innerHTML = data.students.map(student => `<div class="student-card">
      <div class="sc-header"><div class="sc-avatar" style="background:linear-gradient(135deg,var(--sky),var(--accent));">${esc(initials(student))}</div><div><div class="sc-name">${esc(`${student.prenom || ''} ${student.nom || ''}`.trim())}</div><div class="sc-matric">${esc(student.email)}</div></div></div>
      <div class="sc-stats"><span class="sc-tag tag-online">● Inscrit</span><span class="sc-tag tag-blue">${esc(student.promotion || 'Promotion')}</span></div>
      <div class="sc-actions"><button class="sc-btn msg" onclick="window.openDashboardConversation ? openDashboardConversation('${esc(student.id)}', this.closest('.student-card')) : location.href='conversation.php?with=${esc(student.id)}'">💬 Message</button><button class="sc-btn view">👁 Profil</button></div>
    </div>`).join('');
  }

  function courseTitle(course) {
    const promotion = course.promotion && course.promotion !== 'Aucune promotion' ? ` - ${course.promotion}` : '';
    return `${course.nom || 'Cours'}${promotion}`;
  }

  function courseSubtitle(course) {
    const count = Number(course.student_count || 0);
    const students = count > 1 ? `${count} etudiants` : `${count} etudiant`;
    return `${course.enseignants || 'Aucun enseignant'} - ${students}`;
  }

  function courseItemHtml(course, index) {
    const gradients = [
      'linear-gradient(135deg,#3b82f6,#1d4ed8)',
      'linear-gradient(135deg,#14b8a6,#0d9488)',
      'linear-gradient(135deg,#6366f1,#4f46e5)',
      'linear-gradient(135deg,#f59e0b,#d97706)'
    ];
    const active = index === 0 ? ' active' : '';
    const badge = Number(course.message_count || 0) > 0 ? `<div class="conv-badge">${esc(course.message_count)}</div>` : '';
    return `<div class="conv-item${active}" data-course-id="${esc(course.id)}" data-course-title="${esc(courseTitle(course))}" data-course-subtitle="${esc(courseSubtitle(course))}" onclick="window.openCourseConversation ? openCourseConversation(this) : selectConv(this)">
      <div class="avatar avatar-group" style="background:${gradients[index % gradients.length]};">FC</div>
      <div class="conv-info">
        <div class="conv-name">${esc(courseTitle(course))}</div>
        <div class="conv-preview">${esc(courseSubtitle(course))}</div>
      </div>
      <div class="conv-meta">
        <div class="conv-time">${esc(course.code || 'Cours')}</div>
        ${badge}
      </div>
    </div>`;
  }

  function replaceCourseSection(panel) {
    const courses = Array.isArray(data.courses) ? data.courses : [];
    if (!panel || !courses.length || panel.dataset.coursesRendered === '1') return;

    const labels = Array.from(panel.children).filter(child => child.classList.contains('section-label'));
    const label = labels.find(child => normalizeSearch(child.textContent).includes('cours'));
    if (!label) return;

    const children = Array.from(panel.children);
    const start = children.indexOf(label) + 1;
    for (let index = start; index < children.length; index += 1) {
      const child = children[index];
      if (child.classList.contains('section-label')) break;
      if (child.classList.contains('conv-item')) child.remove();
    }

    label.insertAdjacentHTML('afterend', courses.slice(0, 8).map(courseItemHtml).join(''));
    panel.dataset.coursesRendered = '1';
  }

  function findPanelSection(title) {
    const wanted = normalizeSearch(title);
    return Array.from(document.querySelectorAll('.panel-section')).find(section => {
      const current = section.querySelector('.panel-title')?.textContent || '';
      return normalizeSearch(current).includes(wanted);
    });
  }

  function renderCourseInfoPanels() {
    const courses = Array.isArray(data.courses) ? data.courses : [];
    if (!courses.length) return;
    const firstCourse = courses[0];

    const infoSection = findPanelSection('infos du cours');
    const infoCard = infoSection?.querySelector('.info-card');
    if (infoCard) {
      const title = infoCard.querySelector('h4');
      const paragraph = infoCard.querySelector('p');
      const tags = infoCard.querySelectorAll('.tag');
      if (title) title.textContent = firstCourse.nom || 'Cours';
      if (paragraph) paragraph.textContent = `${firstCourse.code || 'Code'} - ${firstCourse.enseignants || 'Aucun enseignant'}`;
      if (tags[0]) tags[0].textContent = firstCourse.promotion || 'Promotion';
      if (tags[1]) tags[1].textContent = `${Number(firstCourse.student_count || 0)} membres`;
    }

    const teacherCoursesSection = findPanelSection('mes cours');
    if (teacherCoursesSection) {
      teacherCoursesSection.querySelectorAll('.info-card').forEach(card => card.remove());
      teacherCoursesSection.insertAdjacentHTML('beforeend', courses.slice(0, 4).map(course => `<div class="info-card">
        <h4>${esc(course.nom || 'Cours')}</h4>
        <p>${esc(course.promotion || 'Promotion')} - ${esc(course.code || '')} - ${esc(Number(course.student_count || 0))} etudiants</p>
      </div>`).join(''));
    }
  }

  function renderCourses() {
    replaceCourseSection(document.getElementById('msgs-panel'));
    replaceCourseSection(document.querySelector('.sidebar .conv-list:not(#msgs-panel)'));
    renderCourseInfoPanels();

    const activeStudentCourse = document.querySelector('#msgs-panel .conv-item[data-course-id].active');
    if (activeStudentCourse && window.openCourseConversation) {
      window.openCourseConversation(activeStudentCourse);
    }
  }

  function renderPrivateContacts() {
    const contacts = Array.isArray(data.privateContacts) ? data.privateContacts : [];
    if (!contacts.length) return;

    const panels = [
      document.getElementById('msgs-panel'),
      document.querySelector('.sidebar .conv-list:not(#msgs-panel)')
    ].filter(Boolean);

    panels.forEach(panel => {
      if (panel.querySelector('.db-private-section')) return;
      const hasStaticPrivateSection = Array.from(panel.querySelectorAll('.section-label')).some(label => {
        const text = label.textContent.toLowerCase();
        return text.includes('priv') || text.includes('enseignant');
      });
      if (panel.id === 'msgs-panel' && hasStaticPrivateSection) {
        window.initDashboardConversationPalette?.();
        return;
      }
      const html = `<div class="section-label db-private-section">Contacts privés</div>` + contacts.slice(0, 8).map(contact => {
        const name = `${contact.prenom || ''} ${contact.nom || ''}`.trim() || contact.email;
        return `<div class="conv-item db-private-section" data-user-id="${esc(contact.id)}" onclick="window.openDashboardConversation ? openDashboardConversation('${esc(contact.id)}', this) : location.href='conversation.php?with=${esc(contact.id)}'">
          <div class="avatar avatar-blue" style="font-size:13px;font-weight:700;">${esc(initials(contact))}</div>
          <div class="conv-info"><div class="conv-name">${esc(name)}</div><div class="conv-preview">${esc(contact.role_nom || 'Contact')}</div></div>
          <div class="conv-meta"><div class="conv-time">Privé</div></div>
        </div>`;
      }).join('');
      panel.insertAdjacentHTML('beforeend', html);
    });
    window.initDashboardConversationPalette?.();
  }

  function renderMurPosts() {
    const posts = document.getElementById('mur-posts');
    if (!posts || !Array.isArray(data.murPosts) || !data.murPosts.length) return;
    posts.innerHTML = data.murPosts.map(post => `<div class="mur-post">
      <div class="post-header"><div class="post-avatar" style="background:linear-gradient(135deg,#f59e0b,#d97706);">${esc(initials(post))}</div><div><div class="post-author">${esc(`${post.prenom || ''} ${post.nom || ''}`.trim())}</div><div class="post-meta">${esc(dateText(post.created_at))} · ${esc(post.cours_nom || '')}</div></div></div>
      <div class="post-content">${esc(post.contenu)}</div>
    </div>`).join('');
  }

  function renderMessages() {
    const box = document.getElementById('messages') || document.getElementById('view-msgs') || document.getElementById('privMsgs');
    if (!box || !Array.isArray(data.messages) || !data.messages.length) return;
    box.innerHTML = data.messages.map(message => `<div class="msg-row">
      <div class="msg-avatar" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">${esc(initials(message))}</div>
      <div class="msg-group"><div class="bubble">${esc(message.contenu)}</div><div class="msg-meta">${esc(dateText(message.created_at))} · ${esc(message.role_nom || '')}</div></div>
    </div>`).join('');
    box.scrollTop = box.scrollHeight;
  }

  function renderValveGrid() {
    const grid = document.querySelector('.annonces-grid');
    if (!grid || !Array.isArray(data.annonces) || !data.annonces.length) return;
    grid.innerHTML = data.annonces.map(annonce => `<div class="annonce-card">
      <div class="ac-header"><div class="ac-cat-icon" style="background:rgba(34,197,94,0.12);">📢</div><div class="ac-meta"><div class="ac-cat-label" style="color:#16a34a;">${esc((annonce.categorie || 'Information').toUpperCase())}</div><div class="ac-title">${esc(annonce.titre)}</div></div></div>
      <div class="ac-body"><div class="ac-text">${esc(annonce.contenu)}</div><div class="ac-footer"><div class="ac-author"><div class="ac-author-ava" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">${esc(initials(annonce))}</div><div><div class="ac-author-name">${esc(`${annonce.prenom || ''} ${annonce.nom || ''}`.trim())}</div></div></div><div class="ac-date">${esc(dateText(annonce.created_at))}</div></div></div>
    </div>`).join('');
  }

  window.deleteAnnonce = window.deleteAnnonce || async function (button) {
    const item = button?.closest?.('.annonce-item');
    const id = item?.dataset?.id || button?.dataset?.id;
    if (!id) {
      alert('Annonce chargee localement. Rechargez la page avant suppression.');
      return;
    }

    if (!confirm('Supprimer cette annonce du Valve ?')) return;
    const form = new FormData();
    form.append('id', id);
    if (data.csrfToken) form.append('csrf_token', data.csrfToken);

    const response = await fetch('index.php?action=valve_delete', {
      method: 'POST',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: form
    });
    if (!response.ok) {
      alert(await response.text());
      return;
    }
    item?.remove();
  };

  window.openEditModal = window.openEditModal || function () {
    alert('Edition disponible depuis l espace Apparitaire.');
  };

  function initUploadButtons() {
    const uploadButtons = Array.from(document.querySelectorAll('.toolbar-btn, .attach-btn'))
      .filter(button => /fichier|image|pdf|vid/i.test(button.textContent || ''));

    uploadButtons.forEach(button => {
      button.addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = button.textContent.toLowerCase().includes('image')
          ? 'image/*'
          : button.textContent.toLowerCase().includes('vid')
            ? 'video/*'
            : '.pdf,.doc,.docx,image/*,video/*';
        input.addEventListener('change', async () => {
          if (!input.files?.[0]) return;
          const form = new FormData();
          form.append('file', input.files[0]);
          if (data.csrfToken) form.append('csrf_token', data.csrfToken);

          try {
            const response = await fetch('index.php?action=file_upload', {
              method: 'POST',
              headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
              body: form
            });
            if (!response.ok) throw new Error(await response.text());
            alert('Fichier envoye avec succes.');
          } catch (error) {
            alert(error.message || 'Erreur pendant l envoi du fichier.');
          }
        });
        input.click();
      });
    });
  }

  function normalizeSearch(value) {
    return String(value ?? '')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .trim();
  }

  function elementSearchText(element) {
    return normalizeSearch([
      element.dataset.search || '',
      element.dataset.name || '',
      element.textContent || ''
    ].join(' '));
  }

  function bindSearchInput(input, getItems, afterFilter = null) {
    if (!input || input.dataset.searchReady === '1') return;
    input.dataset.searchReady = '1';

    const filter = () => {
      const query = normalizeSearch(input.value);
      getItems().forEach(item => {
        const visible = query === '' || elementSearchText(item).includes(query);
        item.style.display = visible ? '' : 'none';
      });
      if (afterFilter) afterFilter();
    };

    input.addEventListener('input', filter);
    filter();
  }

  function refreshSectionLabels(panel) {
    if (!panel) return;
    const children = Array.from(panel.children);
    children.forEach((child, index) => {
      if (!child.classList.contains('section-label')) return;
      const nextItems = [];
      for (let i = index + 1; i < children.length; i += 1) {
        if (children[i].classList.contains('section-label')) break;
        if (children[i].classList.contains('conv-item')) nextItems.push(children[i]);
      }
      child.style.display = nextItems.some(item => item.style.display !== 'none') ? '' : 'none';
    });
  }

  function initSidebarSearch() {
    document.querySelectorAll('.sidebar-search .search-input').forEach(input => {
      const sidebar = input.closest('.sidebar');
      const panel = sidebar?.querySelector('.conv-list');
      if (!panel) return;
      bindSearchInput(input, () => Array.from(panel.querySelectorAll('.conv-item')), () => refreshSectionLabels(panel));
    });
  }

  function initStudentSearch() {
    document.querySelectorAll('.search-students input').forEach(input => {
      bindSearchInput(input, () => Array.from(document.querySelectorAll('.students-grid .student-card')));
    });
  }

  function initValveSearch() {
    const input = document.querySelector('.search-valve input');
    const grid = document.querySelector('.annonces-grid');
    if (!input || !grid) return;

    bindSearchInput(input, () => Array.from(grid.querySelectorAll('.annonce-card')));

    const observer = new MutationObserver(() => {
      input.dispatchEvent(new Event('input'));
    });
    observer.observe(grid, { childList: true });
  }

  function initSearchFilters() {
    initSidebarSearch();
    initStudentSearch();
    initValveSearch();
  }

  updateProfile();
  updateStats();
  renderAdminUsers();
  renderAnnonceList();
  renderStudents();
  renderCourses();
  renderPrivateContacts();
  renderMurPosts();
  renderMessages();
  renderValveGrid();
  initUploadButtons();
  initSearchFilters();
})();
