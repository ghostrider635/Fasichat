function setRole(btn, roleValue) {
  document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const role = roleValue || btn.textContent.trim();
  const hiddenRole = document.getElementById('role_selectionne');
  if (hiddenRole) {
    hiddenRole.value = role;
  }
}
