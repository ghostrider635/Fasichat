/**
 * Utilitaires de sécurité pour prévenir les vulnérabilités XSS
 */

const Security = {
    /**
     * Sanitizer le HTML pour prévenir XSS
     * @param {string} html - HTML à sanitizer
     * @returns {string} HTML sanitizé
     */
    sanitizeHTML: function(html) {
        if (typeof html !== 'string') return '';
        
        const div = document.createElement('div');
        div.textContent = html;
        return div.innerHTML;
    },
    
    /**
     * Échapper les caractères spéciaux HTML
     * @param {string} text - Texte à échapper
     * @returns {string} Texte échappé
     */
    escapeHTML: function(text) {
        if (typeof text !== 'string') return '';
        
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;')
            .replace(/\\/g, '&#x2F;');
    },
    
    /**
     * Sécuriser l'insertion de contenu dans le DOM
     * @param {HTMLElement} element - Élément cible
     * @param {string} content - Contenu à insérer
     * @param {boolean} useTextContent - Utiliser textContent au lieu de innerHTML
     */
    safeInsert: function(element, content, useTextContent = true) {
        if (!element || !(element instanceof HTMLElement)) return;
        
        if (useTextContent) {
            element.textContent = content;
        } else {
            element.innerHTML = this.sanitizeHTML(content);
        }
    },
    
    /**
     * Valider une URL pour prévenir les attaques
     * @param {string} url - URL à valider
     * @returns {boolean} True si l'URL est valide
     */
    isValidURL: function(url) {
        try {
            const parsed = new URL(url);
            // Autoriser seulement HTTP/HTTPS
            return parsed.protocol === 'http:' || parsed.protocol === 'https:';
        } catch {
            return false;
        }
    },
    
    /**
     * Valider une adresse email
     * @param {string} email - Email à valider
     * @returns {boolean} True si l'email est valide
     */
    isValidEmail: function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },
    
    /**
     * Prévenir les injections SQL (côté client basique)
     * @param {string} input - Entrée à valider
     * @returns {boolean} True si l'entrée est sûre
     */
    isSafeInput: function(input) {
        if (typeof input !== 'string') return false;
        
        // Liste de mots-clés SQL dangereux
        const sqlKeywords = [
            'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'UNION', 
            'OR', 'AND', 'WHERE', 'FROM', 'TABLE', 'DATABASE'
        ];
        
        const upperInput = input.toUpperCase();
        return !sqlKeywords.some(keyword => upperInput.includes(keyword));
    },
    
    /**
     * Générer un token CSRF côté client
     * @returns {string} Token CSRF
     */
    generateCsrfToken: function() {
        const array = new Uint8Array(32);
        window.crypto.getRandomValues(array);
        return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
    },
    
    /**
     * Ajouter un token CSRF à un formulaire
     * @param {HTMLFormElement} form - Formulaire
     */
    addCsrfToForm: function(form) {
        const token = this.generateCsrfToken();
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'csrf_token';
        input.value = token;
        form.appendChild(input);
        
        // Stocker le token dans sessionStorage
        sessionStorage.setItem('csrf_token', token);
    },
    
    /**
     * Vérifier un token CSRF
     * @param {string} token - Token à vérifier
     * @returns {boolean} True si le token est valide
     */
    verifyCsrfToken: function(token) {
        const storedToken = sessionStorage.getItem('csrf_token');
        return storedToken && storedToken === token;
    }
};

// Exporter pour utilisation globale
window.Security = Security;

// Remplacer les méthodes dangereuses par défaut
document.addEventListener('DOMContentLoaded', function() {
    // Surveiller l'utilisation de innerHTML
    const originalInnerHTML = Object.getOwnPropertyDescriptor(Element.prototype, 'innerHTML');
    
    Object.defineProperty(Element.prototype, 'innerHTML', {
        set: function(value) {
            if (typeof value === 'string') {
                const sanitized = Security.sanitizeHTML(value);
                originalInnerHTML.set.call(this, sanitized);
            } else {
                originalInnerHTML.set.call(this, value);
            }
        },
        get: originalInnerHTML.get,
        configurable: true
    });
    
    // Ajouter un avertissement pour eval()
    const originalEval = window.eval;
    window.eval = function(code) {
        console.warn('⚠️ L\'utilisation de eval() est dangereuse et déconseillée.');
        console.warn('Code:', code.substring(0, 100) + (code.length > 100 ? '...' : ''));
        return originalEval(code);
    };
});

// Helper pour utiliser textContent au lieu de innerHTML
function safeText(element, text) {
    if (element && element.textContent !== undefined) {
        element.textContent = text;
    }
}

// Helper pour créer des éléments sécurisés
function createSafeElement(tag, attributes = {}, text = '') {
    const element = document.createElement(tag);
    
    // Ajouter les attributs
    Object.entries(attributes).forEach(([key, value]) => {
        if (key.startsWith('on')) {
            console.warn(`⚠️ L'attribut ${key} peut être dangereux.`);
        }
        element.setAttribute(key, Security.escapeHTML(value));
    });
    
    // Ajouter le texte
    if (text) {
        safeText(element, text);
    }
    
    return element;
}