<?php require __DIR__ . '/_bootstrap.php'; requireDashboardRoles(['Vice-Doyen']); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FasiChat — Vice-Doyen</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/dashboard_vicedoyen.css">
</head>
<body>
<div class="sidebar">
  <div class="sidebar-header">
    <div class="brand-mark">🏅</div>
    <div class="brand-info"><h3>FasiChat Admin</h3><span>Espace Vice-Doyen</span></div>
  </div>
  <div class="role-badge-sidebar"><div class="rdot"></div><span>VICE-DOYEN — Accès administratif</span></div>
<div id="leftMenuMount"></div>

  <div class="sidebar-bottom">
    <div class="profile-ava"><div class="online-dot"></div>🏅</div>
    <div class="profile-info"><h4>Pr. MANPUYA</h4><span>Vice-Doyen</span></div>
    <a href="index.php?action=logout" class="logout-btn">🚪</a>
  </div>
</div>

<div class="main-area">
  <div class="admin-topbar">
    <div>
      <div class="topbar-title">Tableau de bord — Vice-Doyen</div>
      <div class="topbar-sub">Faculté des Sciences Informatiques </div>
    </div>
    <div class="topbar-right">
      <button class="tb-btn ghost" onclick="location.href='valve.php'">📣 Valve</button>
      <button class="tb-btn ghost" onclick="location.href='dashboard_admin.php'">🏛 Espace Doyen</button>
      <button class="tb-btn primary" onclick="openModal()">📅 Convoquer une réunion</button>
    </div>
  </div>
  <div class="admin-content">
    <div class="stats-row" id="vdStats">
      <div class="stat-card purple"><div class="stat-icon">🔬</div><div class="stat-number"></div><div class="stat-label">Projets de recherche</div><div class="stat-trend">En cours ce semestre</div></div>
      <div class="stat-card blue"><div class="stat-icon">👨‍🏫</div><div class="stat-number"></div><div class="stat-label">Enseignants-chercheurs</div><div class="stat-trend">Commission de recherche</div></div>
      <div class="stat-card gold"><div class="stat-icon">📅</div><div class="stat-number"></div><div class="stat-label">Convocation envoyée</div><div class="stat-trend">Ce mois-ci</div></div>
      <div class="stat-card green"><div class="stat-icon">📣</div><div class="stat-number"></div><div class="stat-label">Annonces Valve</div><div class="stat-trend">Publications actives</div></div>
    </div>
    <div class="two-col">
      <!-- CONVOC -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">📅 Convoquer une réunion</div>
          <span class="conf-badge">🏅 Vice-Doyen uniquement</span>
        </div>
        <div class="convoc-form">
          <div class="form-group"><label class="form-label">Objet *</label><input type="text" class="form-input" placeholder="Ex: Commission de recherche S5..."></div>
          <div class="form-row-2">
            <div class="form-group"><label class="form-label">Date *</label><input type="date" class="form-input"></div>
            <div class="form-group"><label class="form-label">Heure *</label><input type="time" class="form-input"></div>
          </div>
          <div class="form-group"><label class="form-label">Lieu / Lien *</label><input type="text" class="form-input" placeholder="Salle de Conférence B..."></div>
          <div class="form-group"><label class="form-label">Message complémentaire</label><textarea class="form-textarea" placeholder="Ordre du jour, documents à préparer..."></textarea></div>
          <div class="form-group"><label class="form-label">Destinataires</label>
            <div class="recipients-box">
              <div class="recipient-tag">👨‍🏫 Tous les enseignants (24)</div>
              <div class="recipient-tag">📋 Tous les assistants (6)</div>
            </div>
          </div>
          <button class="send-btn" onclick="sendConvoc()">📨 Envoyer la convocation</button>
        </div>
      </div>
      <!-- RIGHT -->
      <div style="display:flex;flex-direction:column;gap:16px;">
        <!-- MSG PRIVE DOYEN -->
        <div class="card">
          <div class="card-header">
            <div class="card-title">🔒 Message privé — Doyen</div>
            <span class="conf-badge">Confidentiel</span>
          </div>
          <div class="priv-chat">
            <div class="priv-messages" id="privMsgs">
              <div class="msg-row">
                <div class="msg-av" style="background:linear-gradient(135deg,#dc2626,#991b1b);">D</div>
                <div class="msg-group">
                  <div class="bubble theirs">Chargement du message confidentiel...</div>
                  <div class="msg-time">...</div>
                </div>
              </div>
            </div>
            <div class="priv-input">
              <input type="file" id="mediaInput" style="display:none;" />
              <div class="priv-toolbar">
                <button class="toolbar-btn" type="button" data-accept=".pdf,.doc,.docx,image/*,video/*,audio/*">📎 Fichier</button>
                <button class="toolbar-btn" type="button" data-accept="image/*">🖼 Image</button>
                <button class="toolbar-btn" type="button" data-accept=".pdf">📊 PDF</button>
                <button class="toolbar-btn" type="button" data-accept="audio/*">🎤 Voice</button>
              </div>
              <textarea class="priv-textarea" placeholder="Message confidentiel au Doyen..." id="privInput" onkeydown="handlePrivKey(event)" rows="1"></textarea>
              <button class="priv-send" onclick="sendPrivMsg()" type="button">➤</button>
            </div>

          </div>
        </div>
        <!-- ACTIVITY -->
        <div class="card">
          <div class="card-header"><div class="card-title">🕐 Activité récente</div></div>
          <div class="activity-list" id="vdRecentActivity"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="convocModal" onclick="closeOut(event)">
  <div class="modal">
    <div class="modal-header">
      <span style="font-size:26px;">📅</span>
      <div><h3>Convoquer une réunion</h3><p>Envoyée à tous les enseignants et assistants</p></div>
      <button class="modal-close-btn" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Objet *</label><input type="text" class="form-input" id="mObj" placeholder="Objet de la réunion..."></div>
      <div class="form-row-2">
        <div class="form-group"><label class="form-label">Date *</label><input type="date" class="form-input" id="mDate"></div>
        <div class="form-group"><label class="form-label">Heure *</label><input type="time" class="form-input" id="mHeure"></div>
      </div>
      <div class="form-group"><label class="form-label">Lieu *</label><input type="text" class="form-input" id="mLieu" placeholder="Salle ou lien..."></div>
      <div class="form-group"><label class="form-label">Message</label><textarea class="form-textarea" id="mMsg" placeholder="Ordre du jour..."></textarea></div>
      <div class="form-group"><label class="form-label">Destinataires</label>
        <div class="recipients-box">
          <div class="recipient-tag">👨‍🏫 Tous les enseignants (24)</div>
          <div class="recipient-tag">📋 Tous les assistants (6)</div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Annuler</button>
      <button class="btn-send-modal" onclick="sendModal()">📨 Envoyer</button>
    </div>
  </div>
</div>

<script>
function setNav(el){document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));el.classList.add('active');}
function openModal(){document.getElementById('convocModal').classList.add('open');}
function closeModal(){document.getElementById('convocModal').classList.remove('open');}
function closeOut(e){if(e.target.id==='convocModal')closeModal();}
function sendModal(){const o=document.getElementById('mObj').value.trim();if(!o){alert('Veuillez saisir l\'objet.');return;}closeModal();alert('Convocation envoyée avec succès !');}
function sendConvoc(){const f=document.querySelector('.convoc-form .form-input');if(!f.value.trim()){alert('Veuillez saisir l\'objet.');return;}alert('Convocation envoyée à 30 destinataires !');}
function handlePrivKey(e){if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendPrivMsg();}}
function sendPrivMsg(){
  const ta=document.getElementById('privInput');
  const text=ta.value.trim();if(!text)return;
  const box=document.getElementById('privMsgs');
  const now=new Date();const time=now.getHours().toString().padStart(2,'0')+':'+now.getMinutes().toString().padStart(2,'0');
  const row=document.createElement('div');row.className='msg-row mine';
  row.innerHTML=`<div class="msg-av" style="background:linear-gradient(135deg,var(--purple),#5b21b6);">VD</div><div class="msg-group"><div class="bubble mine">${text.replace(/</g,'&lt;')}</div><div class="msg-time">${time} ✓</div></div>`;
  box.appendChild(row);ta.value='';box.scrollTop=box.scrollHeight;
}
async function apiPostBackend(action,data){
  if(data instanceof FormData&&window.FASI_PAGE_DATA?.csrfToken){data.set('csrf_token',window.FASI_PAGE_DATA.csrfToken);}
  const response=await fetch(`index.php?action=${action}`,{method:'POST',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},body:data});
  if(!response.ok)throw new Error(await response.text());
  return response.json();
}
function appendRecipientsBackend(data){data.append('destinataires[]','Enseignant');data.append('destinataires[]','Assistant');}
sendModal=async function(){const o=document.getElementById('mObj').value.trim();if(!o){alert('Veuillez saisir l\'objet.');return;}const data=new FormData();data.append('objet',o);data.append('date',document.getElementById('mDate').value);data.append('heure',document.getElementById('mHeure').value);data.append('lieu',document.getElementById('mLieu').value);data.append('message',document.getElementById('mMsg').value);appendRecipientsBackend(data);try{await apiPostBackend('convocation_create',data);closeModal();alert('Convocation envoyée avec succès !');}catch(error){alert(error.message||'Erreur pendant l\'envoi.');}};
sendConvoc=async function(){
  const form=document.querySelector('.convoc-form');
  const fields=form.querySelectorAll('.form-input');
  if(!fields[0].value.trim()){alert('Veuillez saisir l\'objet.');return;}
  const data=new FormData();
  data.append('objet',fields[0].value);
  data.append('date',fields[1].value);
  data.append('heure',fields[2].value);
  data.append('lieu',fields[3].value);
  data.append('message',form.querySelector('.form-textarea')?.value||'');

  // destinataires : enseignants + assistants (DB côté backend)
  appendRecipientsBackend(data);

  try{await apiPostBackend('convocation_create',data);closeModal();alert('Convocation envoyée avec succès !');}catch(error){alert(error.message||'Erreur pendant l\'envoi.');}
};

sendPrivMsg=async function(){const ta=document.getElementById('privInput');const text=ta.value.trim();if(!text)return;const data=new FormData();data.append('receiver_id','1');data.append('content',text);try{await apiPostBackend('message_send',data);}catch(error){alert(error.message||'Erreur pendant l\'envoi du message.');return;}const box=document.getElementById('privMsgs');const now=new Date();const time=now.getHours().toString().padStart(2,'0')+':'+now.getMinutes().toString().padStart(2,'0');const row=document.createElement('div');row.className='msg-row mine';row.innerHTML=`<div class="msg-av" style="background:linear-gradient(135deg,var(--purple),#5b21b6);">VD</div><div class="msg-group"><div class="bubble mine">${text.replace(/</g,'&lt;')}</div><div class="msg-time">${time} OK</div></div>`;box.appendChild(row);ta.value='';box.scrollTop=box.scrollHeight;};
window.addEventListener('load',()=>{const b=document.getElementById('privMsgs');b.scrollTop=b.scrollHeight;});
</script>
<script>
window.FASI_PAGE_DATA = <?= json_encode(pageData(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
</script>
<script>
function vdEsc(value){return String(value??'').replace(/[&<>"']/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));}
function vdInitials(user){return `${user?.prenom?.[0]||''}${user?.nom?.[0]||''}`.toUpperCase()||'FC';}
function vdName(user){return `${user?.prenom||''} ${user?.nom||''}`.trim()||user?.email||'Discussion';}
function vdDate(value){return value?new Date(String(value).replace(' ','T')).toLocaleString('fr-FR'):'';}
function vdDoyenContact(){return (window.FASI_PAGE_DATA?.privateContacts||[]).find(contact=>String(contact.role_nom||'').includes('Doyen'));}
function renderViceDoyenThread(payload){
  const box=document.getElementById('privMsgs');
  const messages=Array.isArray(payload.messages)?payload.messages:[];
  if(!messages.length){
    box.innerHTML=`<div class="msg-row"><div class="msg-av" style="background:linear-gradient(135deg,#dc2626,#991b1b);">${vdEsc(vdInitials(payload.other_user))}</div><div class="msg-group"><div class="bubble theirs">Aucun message confidentiel pour le moment.</div><div class="msg-time">Maintenant</div></div></div>`;
    return;
  }
  box.innerHTML=messages.map(message=>{
    const mine=Number(message.expediteur_id)===Number(payload.current_user_id);
    const initials=mine?(window.FASI_PAGE_DATA?.userInitials||'VD'):vdInitials(message);
    return `<div class="msg-row${mine?' mine':''}"><div class="msg-av" style="background:${mine?'linear-gradient(135deg,var(--purple),#5b21b6)':'linear-gradient(135deg,#dc2626,#991b1b)'};">${vdEsc(initials)}</div><div class="msg-group"><div class="bubble ${mine?'mine':'theirs'}">${vdEsc(message.contenu)}</div><div class="msg-time">${vdEsc(vdDate(message.created_at))}${mine?' OK':''}</div></div></div>`;
  }).join('');
  box.scrollTop=box.scrollHeight;
}
async function openViceDoyenPrivateChat(item){
  if(item)setNav(item);
  const contact=vdDoyenContact();
  if(!contact){alert('Contact Doyen introuvable.');return;}
  window.FASI_ACTIVE_PRIV_RECEIVER_ID=contact.id;
  const response=await fetch(`index.php?action=conversation_thread&with=${encodeURIComponent(contact.id)}`,{headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
  if(!response.ok){alert(await response.text());return;}
  const payload=await response.json();
  const title=document.querySelector('.card-title');
  if(title&&title.textContent.includes('Message'))title.textContent=`🔒 Message privé — ${vdName(payload.other_user)}`;
  renderViceDoyenThread(payload);
}
sendPrivMsg=async function(){
  const ta=document.getElementById('privInput');
  const text=ta.value.trim();
  const mediaInput=document.getElementById('mediaInput');
  const file=mediaInput?.files?.[0] || null;
  if(!text && !file)return;
  const contact=vdDoyenContact();
  const receiverId=window.FASI_ACTIVE_PRIV_RECEIVER_ID||contact?.id;
  if(!receiverId){alert('Contact Doyen introuvable.');return;}
  const data=new FormData();
  data.append('receiver_id',receiverId);
  data.append('content',text);
  try{await apiPostBackend('message_send',data);}catch(error){alert(error.message||'Erreur pendant l\'envoi du message.');return;}
  await openViceDoyenPrivateChat();
  ta.value='';
};
window.addEventListener('load',()=>openViceDoyenPrivateChat());
</script>
<script src="assets/js/dashboard_doyen_leftmenu_dynamic.js"></script>
<script src="assets/js/dynamic-db.js"></script>
</body>
</html>

