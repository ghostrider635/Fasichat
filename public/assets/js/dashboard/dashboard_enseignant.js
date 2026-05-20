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
  if (btn) { document.querySelectorAll('.nav-tab').forEach(b => b.classList.remove('active')); btn.classList.add('active'); }
}
function selectConv(item, title, icon, bg, sub, type) {
  document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('active'));
  item.classList.add('active');
  document.getElementById('topbarTitle').textContent = title;
  document.getElementById('topbarSub').textContent = sub;
}
function handleKey(e) { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMsg(); } }
function sendMsg() {
  const ta = document.getElementById('msgInput');
  const text = ta.value.trim();
  if (!text) return;
  const msgs = document.getElementById('view-msgs');
  const now = new Date();
  const time = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
  
  // Création sécurisée sans innerHTML
  const row = document.createElement('div');
  row.className = 'msg-row mine';
  
  const avatar = document.createElement('div');
  avatar.className = 'msg-avatar';
  avatar.style.background = 'linear-gradient(135deg,#f59e0b,#d97706)';
  avatar.textContent = 'PM';
  
  const msgGroup = document.createElement('div');
  msgGroup.className = 'msg-group';
  
  const bubble = document.createElement('div');
  bubble.className = 'bubble mine';
  bubble.textContent = text; // textContent au lieu de innerHTML
  
  const meta = document.createElement('div');
  meta.className = 'msg-meta';
  meta.textContent = time + ' ✓';
  
  msgGroup.appendChild(bubble);
  msgGroup.appendChild(meta);
  row.appendChild(avatar);
  row.appendChild(msgGroup);
  
  msgs.appendChild(row);
  ta.value = '';
  ta.style.height = 'auto';
  msgs.scrollTop = msgs.scrollHeight;
}
function publishPost() {
  const ta = document.querySelector('.mur-textarea');
  const text = ta.value.trim();
  if (!text) return;
  const posts = document.getElementById('mur-posts');
  
  // Création sécurisée sans innerHTML
  const post = document.createElement('div');
  post.className = 'mur-post';
  
  const postHeader = document.createElement('div');
  postHeader.className = 'post-header';
  
  const postAvatar = document.createElement('div');
  postAvatar.className = 'post-avatar';
  postAvatar.style.background = 'linear-gradient(135deg,#f59e0b,#d97706)';
  postAvatar.textContent = 'PM';
  
  const headerContent = document.createElement('div');
  
  const postAuthor = document.createElement('div');
  postAuthor.className = 'post-author';
  postAuthor.textContent = 'Prof. Mbaye';
  
  const postMeta = document.createElement('div');
  postMeta.className = 'post-meta';
  postMeta.textContent = 'À l\'instant · PHP POO L3';
  
  const postActions = document.createElement('div');
  postActions.className = 'post-actions';
  
  const editBtn = document.createElement('button');
  editBtn.className = 'post-action-btn';
  editBtn.textContent = '✏️';
  
  const deleteBtn = document.createElement('button');
  deleteBtn.className = 'post-action-btn';
  deleteBtn.textContent = '🗑';
  
  const postContent = document.createElement('div');
  postContent.className = 'post-content';
  postContent.textContent = text; // textContent au lieu de innerHTML
  
  // Assemblage
  headerContent.appendChild(postAuthor);
  headerContent.appendChild(postMeta);
  postActions.appendChild(editBtn);
  postActions.appendChild(deleteBtn);
  postHeader.appendChild(postAvatar);
  postHeader.appendChild(headerContent);
  postHeader.appendChild(postActions);
  post.appendChild(postHeader);
  post.appendChild(postContent);
  
  posts.insertBefore(post, posts.firstChild);
  ta.value = '';
}
document.getElementById('msgInput').addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});