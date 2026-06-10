document.querySelectorAll('.filter-chip').forEach(c => {
  c.addEventListener('click', () => {
    document.querySelectorAll('.filter-chip').forEach(x => x.classList.remove('active'));
    c.classList.add('active');
  });
});

document.querySelectorAll('.cat-item').forEach(c => {
  c.addEventListener('click', function() {
    document.querySelectorAll('.cat-item').forEach(x => x.classList.remove('active'));
    this.classList.add('active');
  });
});

function openModal() {
  document.getElementById('modal').classList.add('open');
}

function closeModal() {
  document.getElementById('modal').classList.remove('open');
}

function closeModalOutside(e) {
  if (e.target === document.getElementById('modal')) closeModal();
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

function cleanCategory(value) {
  return value.replace(/[^\p{L}\p{N}\s-]/gu, '').trim() || 'Information';
}

async function publishAnnonce() {
  const title = document.querySelector('.modal-body .form-input').value.trim();
  if (!title) {
    alert('Veuillez saisir un titre.');
    return;
  }

  const content = document.querySelector('.modal-body .form-textarea').value.trim();
  const category = cleanCategory(document.querySelector('.modal-body .form-select')?.value || 'Information');
  const data = new FormData();
  data.append('titre', title);
  data.append('contenu', content);
  data.append('categorie', category);

  try {
    await apiPost('valve_create', data);
    closeModal();
    alert('Annonce publiee avec succes sur le Valve !');
    loadValveAnnonces();
  } catch (error) {
    alert(error.message || 'Erreur pendant la publication.');
  }
}

function escapeHtml(value) {
  return String(value).replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
}

function annonceCard(annonce) {
  const cat = escapeHtml((annonce.categorie || 'Information').toUpperCase());
  const title = escapeHtml(annonce.titre || '');
  const content = escapeHtml(annonce.contenu || '');
  const author = escapeHtml(`${annonce.prenom || ''} ${annonce.nom || ''}`.trim() || 'FasiChat');
  const date = annonce.created_at ? new Date(annonce.created_at.replace(' ', 'T')).toLocaleString('fr-FR') : '';
  return `<div class="annonce-card">
    <div class="ac-header">
      <div class="ac-cat-icon" style="background:rgba(34,197,94,0.12);">📢</div>
      <div class="ac-meta">
        <div class="ac-cat-label" style="color:#16a34a;">${cat}</div>
        <div class="ac-title">${title}</div>
      </div>
    </div>
    <div class="ac-body">
      <div class="ac-text">${content}</div>
      <div class="ac-footer">
        <div class="ac-author">
          <div class="ac-author-ava" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">${author.slice(0, 2).toUpperCase()}</div>
          <div><div class="ac-author-name">${author}</div></div>
        </div>
        <div class="ac-date">${escapeHtml(date)}</div>
      </div>
    </div>
  </div>`;
}

async function loadValveAnnonces() {
  const grid = document.querySelector('.annonces-grid');
  if (!grid) return;
  try {
    const response = await fetch('index.php?action=valve_list', { headers: { Accept: 'application/json' } });
    if (!response.ok) return;
    const annonces = await response.json();
    if (Array.isArray(annonces) && annonces.length) {
      grid.innerHTML = annonces.map(annonceCard).join('');
    }
  } catch (error) {}
}

window.addEventListener('load', loadValveAnnonces);
