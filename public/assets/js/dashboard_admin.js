function setNav(el) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  el.classList.add('active');
}

function openConvocModal() {
  document.getElementById('convocModal').classList.add('open');
}

function closeConvocModal() {
  document.getElementById('convocModal').classList.remove('open');
}

function closeModalOutside(e) {
  if (e.target.id === 'convocModal') closeConvocModal();
}

async function apiPost(action, data) {
  // CSRF: le bootstrap passe généralement un token dans `window.FASI_PAGE_DATA.csrfToken`
  // Si non présent, le backend va refuser avec 403.
  if (data instanceof FormData) {
    const token = window.FASI_PAGE_DATA?.csrfToken;
    if (token) {
      data.set('csrf_token', token);
    }
  }

  const response = await fetch(`index.php?action=${action}`, {
    method: 'POST',
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: data
  });
  if (!response.ok) throw new Error(await response.text());
  return response.json();
}


async function sendConvocation(data, successMessage) {
  try {
    await apiPost('convocation_create', data);
    alert(successMessage);
  } catch (error) {
    alert(error.message || "Erreur pendant l'envoi de la convocation.");
  }
}

function appendDefaultRecipients(data) {
  // backend attend des IDs de destinataires (destinataires[])
  // on envoie donc les rôles comme fallback si nécessaire.
  data.append('destinataires[]', 'Enseignant');
  data.append('destinataires[]', 'Assistant');
}


async function sendConvocModal() {
  const obj = document.getElementById('convocObj').value.trim();
  if (!obj) {
    alert("Veuillez saisir l'objet de la reunion.");
    return;
  }

  const data = new FormData();
  data.append('objet', obj);
  data.append('date', document.getElementById('convocDate').value);
  data.append('heure', document.getElementById('convocHeure').value);
  data.append('lieu', document.getElementById('convocLieu').value);
  data.append('message', document.getElementById('convocMsg').value);
  appendDefaultRecipients(data);

  await sendConvocation(data, 'Convocation envoyee avec succes a 30 destinataires !');
  closeConvocModal();
}

async function sendConvoc() {
  const form = document.querySelector('.convoc-form');
  const fields = form.querySelectorAll('.form-input');
  const obj = fields[0].value.trim();
  if (!obj) {
    alert("Veuillez saisir l'objet de la reunion.");
    return;
  }

  const data = new FormData();
  data.append('objet', obj);
  data.append('date', fields[1].value);
  data.append('heure', fields[2].value);
  data.append('lieu', fields[3].value);
  data.append('message', form.querySelector('.form-textarea')?.value || '');
  appendDefaultRecipients(data);

  await sendConvocation(data, 'Convocation envoyee avec succes a tous les enseignants et assistants !');
}
