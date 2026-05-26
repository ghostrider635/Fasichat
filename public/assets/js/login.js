function setRole(btn) {
  document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const roleText = btn.textContent.trim().toLowerCase();
  const roleInput = document.getElementById('roleInput');

  if (roleText.includes('enseignant')) {
    roleInput.value = 'enseignant';
  } else if (roleText.includes('assistant')) {
    roleInput.value = 'assistant';
  } else {
    roleInput.value = 'etudiant';
  }
}

// Checkbox toggle
document.querySelectorAll('.checkbox-wrap').forEach(wrap => {
  wrap.addEventListener('click', () => {
    const cb = wrap.querySelector('input');
    cb.checked = !cb.checked;
  });
});