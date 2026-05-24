// JavaScript pour le dashboard Vice-Doyen

// Fonction pour gérer la navigation
function setNav(element) {
    // Retirer la classe active de tous les éléments de navigation
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Ajouter la classe active à l'élément cliqué
    element.classList.add('active');
}

// Fonction pour ouvrir le modal de convocation
function openModal() {
    const modal = document.getElementById('convocModal');
    if (modal) {
        modal.classList.add('open');
    }
}

// Fonction pour fermer le modal
function closeModal() {
    const modal = document.getElementById('convocModal');
    if (modal) {
        modal.classList.remove('open');
    }
}

// Fonction pour envoyer une convocation
function sendConvoc() {
    const objet = document.querySelector('.convoc-form input[type=\"text\"]').value;
    const date = document.querySelector('.convoc-form input[type=\"date\"]').value;
    const heure = document.querySelector('.convoc-form input[type=\"time\"]').value;
    const lieu = document.querySelector('.convoc-form input[placeholder=\"Salle de Conférence B...\"]').value;
    const message = document.querySelector('.convoc-form textarea').value;
    
    if (!objet || !date || !heure || !lieu) {
        alert('Veuillez remplir tous les champs obligatoires (*)');
        return;
    }
    
    // Simuler l'envoi de la convocation
    console.log('Convocation envoyée:', { objet, date, heure, lieu, message });
    
    // Afficher un message de succès
    alert('Convocation envoyée avec succès !');
    
    // Réinitialiser le formulaire
    document.querySelector('.convoc-form').reset();
}

// Fonction pour gérer les touches dans le chat privé
function handlePrivKey(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendPrivMsg();
    }
    
    // Ajuster la hauteur du textarea
    const textarea = event.target;
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
}

// Fonction pour envoyer un message privé
function sendPrivMsg() {
    const input = document.getElementById('privInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    const messagesContainer = document.getElementById('privMsgs');
    
    // Créer un nouveau message
    const messageRow = document.createElement('div');
    messageRow.className = 'msg-row mine';
    
    const messageAv = document.createElement('div');
    messageAv.className = 'msg-av';
    messageAv.style.background = 'linear-gradient(135deg, var(--purple), #5b21b6)';
    messageAv.textContent = 'VD';
    
    const messageGroup = document.createElement('div');
    messageGroup.className = 'msg-group';
    
    const bubble = document.createElement('div');
    bubble.className = 'bubble mine';
    bubble.textContent = message;
    
    const time = document.createElement('div');
    time.className = 'msg-time';
    const now = new Date();
    time.textContent = now.getHours().toString().padStart(2, '0') + ':' + 
                      now.getMinutes().toString().padStart(2, '0') + ' ✓✓';
    
    messageGroup.appendChild(bubble);
    messageGroup.appendChild(time);
    messageRow.appendChild(messageAv);
    messageRow.appendChild(messageGroup);
    
    messagesContainer.appendChild(messageRow);
    
    // Réinitialiser l'input
    input.value = '';
    input.style.height = 'auto';
    
    // Faire défiler vers le bas
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter le modal au DOM s'il n'existe pas
    if (!document.getElementById('convocModal')) {
        const modalHTML = `
            <div class="modal-overlay" id="convocModal">
                <div class="modal">
                    <div class="modal-header">
                        <div>
                            <h3>📅 Convoquer une réunion</h3>
                            <p>Envoi d'une convocation officielle — Vice-Doyen uniquement</p>
                        </div>
                        <button class="modal-close-btn" onclick="closeModal()">✕</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Objet *</label>
                            <input type="text" class="form-input" placeholder="Ex: Commission de recherche S5...">
                        </div>
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">Date *</label>
                                <input type="date" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Heure *</label>
                                <input type="time" class="form-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lieu / Lien *</label>
                            <input type="text" class="form-input" placeholder="Salle de Conférence B...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Message complémentaire</label>
                            <textarea class="form-textarea" placeholder="Ordre du jour, documents à préparer..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Destinataires</label>
                            <div class="recipients-box">
                                <div class="recipient-tag">👨🏫 Tous les enseignants (24)</div>
                                <div class="recipient-tag">📋 Tous les assistants (6)</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-cancel" onclick="closeModal()">Annuler</button>
                        <button class="btn-send-modal" onclick="sendConvoc()">📨 Envoyer la convocation</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }
    
    // Fermer le modal en cliquant en dehors
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('convocModal');
        if (modal && modal.classList.contains('open') && event.target === modal) {
            closeModal();
        }
    });
    
    // Ajuster la hauteur initiale du textarea
    const textarea = document.getElementById('privInput');
    if (textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight) + 'px';
    }
});