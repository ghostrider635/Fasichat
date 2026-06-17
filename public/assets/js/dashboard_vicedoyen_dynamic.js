(function(){
  const pageData = window.FASI_PAGE_DATA || {};

  function escapeHtml(value){
    return String(value ?? '').replace(/[&<>"']/g, (c)=>({
      '&':'&amp;','<':'<','>':'>','"':'"',"'":'&#039;'
    }[c]));
  }

  async function apiGet(action){
    const response = await fetch(`index.php?action=${action}`,{
      headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}
    });
    if(!response.ok) throw new Error(await response.text());
    return response.json();
  }

  function renderStats(stats){
    const root = document.getElementById('vdStats');
    if(!root || !stats) return;
    const usersEl = root.querySelector('.stat-card.blue .stat-number');
    const teachersEl = root.querySelector('.stat-card.blue .stat-number');
    const projectsEl = root.querySelector('.stat-card.purple .stat-number');
    const convEl = root.querySelector('.stat-card.gold .stat-number');
    const valveEl = root.querySelector('.stat-card.green .stat-number');

    // Mapping
    if(projectsEl && stats.courses_total !== undefined) projectsEl.textContent = stats.courses_total;
    if(teachersEl && stats.teachers_total !== undefined) teachersEl.textContent = stats.teachers_total;
    if(convEl && stats.convocations_total !== undefined) convEl.textContent = stats.convocations_total;
    if(valveEl && stats.annonces_total !== undefined) valveEl.textContent = stats.annonces_total;

  }

  function renderActivity(items){
    const box = document.getElementById('vdRecentActivity');
    if(!box) return;
    if(!Array.isArray(items) || !items.length){
      box.innerHTML = '<div style="opacity:.7;padding:10px;">Aucune activité récente.</div>';
      return;
    }

    box.innerHTML = items.map(it=>{
      const icon = it.type === 'convocation' ? '📅' : '📣';
      const title = it.type === 'convocation' ? 'Convocation envoyée' : 'Annonce Valve publiée';
      const subtitle = escapeHtml(it.subtitle || it.title || '');
      const date = it.created_at ? new Date(String(it.created_at).replace(' ', 'T')).toLocaleString('fr-FR') : '';
      return `
        <div class="activity-item">
          <div class="act-icon-wrap" style="background:rgba(99,102,241,0.1);">${icon}</div>
          <div class="act-text"><strong>${escapeHtml(title)}</strong><p>${subtitle}</p></div>
          <div class="act-time">${escapeHtml(date)}</div>
        </div>
      `;
    }).join('');
  }

  async function init(){
    try{
      const [statsRes, actRes] = await Promise.all([
        apiGet('dashboard_doyen_stats'),
        apiGet('dashboard_doyen_recent_activity')
      ]);
      renderStats(statsRes?.stats || {});
      renderActivity(actRes?.items || []);
    }catch(e){
      // keep silent
      console.error(e);
    }
  }

  window.addEventListener('load', init);
})();

