function setRole(btn) {
  document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const role = btn.textContent.trim();
  const roleInput = document.getElementById('roleInput');
  
  // Convertir le texte du bouton en valeur pour le champ caché
  switch(role) {
    case 'Étudiant':
      roleInput.value = 'etudiant';
      break;
    case 'Enseignant':
      roleInput.value = 'enseignant';
      break;
    case 'Assistant':
      roleInput.value = 'assistant';
      break;
  }
}
// Checkbox toggle
document.querySelectorAll('.checkbox-wrap').forEach(wrap => {
  wrap.addEventListener('click', () => {
    const cb = wrap.querySelector('input');
    cb.checked = !cb.checked;
  });
});