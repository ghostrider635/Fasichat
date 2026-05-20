# FasiChat Classroom - Plateforme de Messagerie Académique

## 📋 Contexte du Projet
**Projet TP** : Programmation Web en PHP - Licence Sciences Informatiques  
**Année académique** : 2025–2026  
**Groupe** : 3 personnes  
**Objectif** : Développer une application web complète en PHP orienté objet pour une plateforme de messagerie académique

## 🎯 Fonctionnalités Principales
- ✅ **Authentification multi-rôles** (6 rôles distincts)
- ✅ **Messagerie avec règles de visibilité** (privé/public)
- ✅ **Système de convocations** (Doyen/Vice-Doyen)
- ✅ **Gestion du Valve** (Apparitaire exclusif)
- ✅ **Partage de fichiers avec compression** (20 Mo max)
- ✅ **Tableaux de bord par rôle**

## 👥 Rôles Utilisateurs
### Acteurs Pédagogiques
- **Étudiant** : Messages privés entre étudiants, messages publics avec enseignants
- **Enseignant** : Gestion des cours, mur pédagogique, convocations
- **Assistant** : Mêmes privilèges que l'enseignant (sauf certaines actions)

### Acteurs Administratifs
- **Doyen** : Convocation de réunions, accès complet
- **Vice-Doyen** : Mêmes droits de convocation que le Doyen
- **Apparitaire** : Gestion exclusive du Valve (annonces institutionnelles)

## 🗺️ Roadmap du Projet - Étapes Clés (2 Semaines)

### **SEMAINE 1 : FONDATIONS & CORE (Jours 1-5)**
**Objectif** : Mettre en place l'infrastructure de base et les fonctionnalités essentielles

#### **Jour 1 : Configuration Initiale** (Toute l'équipe)
- [ ] Créer le dépôt GitHub et configurer Git
- [ ] Installer et configurer l'environnement PHP/MySQL
- [ ] Implémenter `backend/config/Autoloader.php`
- [ ] Créer `backend/config/Database.php` (PDO)
- [ ] Écrire `database/schema.sql` (tables de base)

#### **Jour 2 : Authentification & Sessions** (Membre 3)
- [ ] Implémenter `backend/config/Session.php`
- [ ] Développer `backend/controllers/AuthController.php`
- [ ] Créer `backend/views/login.php`
- [ ] Implémenter login/logout avec validation
- [ ] Ajouter la protection basique des sessions

#### **Jour 3 : Hiérarchie des Utilisateurs** (Membre 1)
- [ ] Implémenter `backend/classes/Utilisateur.php` (abstraite)
- [ ] Créer `Etudiant.php`, `Enseignant.php`, `Assistant.php`
- [ ] Implémenter `Doyen.php` et `ViceDoyen.php` avec interface `Convocable`
- [ ] Créer `Apparitaire.php` avec droits Valve
- [ ] Tester l'héritage avec des données de test

#### **Jour 4 : Système de Messagerie** (Membre 2)
- [ ] Créer `backend/classes/Message.php` (classe de base)
- [ ] Implémenter `MessagePrive.php` et `MessagePublic.php`
- [ ] Développer `backend/classes/Convocation.php`
- [ ] Créer `backend/controllers/MessageController.php`
- [ ] Implémenter les règles de visibilité basiques

#### **Jour 5 : Intégration Initiale** (Toute l'équipe)
- [ ] Connecter l'authentification à la base de données
- [ ] Créer les premières vues PHP (`backend/views/`)
- [ ] Intégrer le CSS/JS existant
- [ ] Tester le flux login → dashboard
- [ ] Préparer la revue de fin de semaine

### **SEMAINE 2 : FONCTIONNALITÉS & FINALISATION (Jours 6-10)**
**Objectif** : Compléter les fonctionnalités et préparer la soutenance

#### **Jour 6 : Gestion du Valve & Fichiers** (Membre 1 + 2)
- [ ] Implémenter `backend/classes/Valve.php` et `AnnonceValve.php`
- [ ] Créer `backend/controllers/ValveController.php`
- [ ] Développer `backend/FileUploader.php` (upload basique)
- [ ] Implémenter `backend/classes/Fichier.php`
- [ ] Ajouter les contrôles d'accès Valve (Apparitaire exclusif)

#### **Jour 7 : Tableaux de Bord & Navigation** (Membre 3)
- [ ] Créer `backend/controllers/DashboardController.php`
- [ ] Développer les vues par rôle : `dashboard_*.php`
- [ ] Implémenter la navigation entre pages
- [ ] Ajouter l'affichage des messages/convocations
- [ ] Créer le mur pédagogique basique

#### **Jour 8 : Sécurité & Validation** (Membre 3)
- [ ] Implémenter `backend/Security.php` et `backend/Validator.php`
- [ ] Ajouter la validation/sanitization de toutes les entrées
- [ ] Implémenter la protection XSS (`htmlspecialchars()`)
- [ ] Renforcer la gestion des sessions
- [ ] Valider les fichiers uploadés (20 Mo max)

#### **Jour 9 : Tests & Optimisation** (Toute l'équipe)
- [ ] Tester chaque rôle avec comptes de test
- [ ] Vérifier toutes les règles de visibilité
- [ ] Tester le système de convocations
- [ ] Valider la gestion du Valve
- [ ] Optimiser les performances et corriger les bugs

#### **Jour 10 : Documentation & Préparation Soutenance** (Membre 2)
- [ ] Compléter la documentation technique
- [ ] Préparer `database/test_data.sql` avec 6 comptes de test
- [ ] Créer le rapport de conception POO (version concise)
- [ ] Préparer la présentation orale (10-15 minutes)
- [ ] Faire une démo finale et corriger les derniers bugs

### **PLAN DE TRAVAIL PARALLÈLE**
**Pour optimiser le temps, travaillez en parallèle sur :**

#### **Tâches en Parallèle (Sémaine 1)**
- **Membre 1** : Classes utilisateurs + hiérarchie POO
- **Membre 2** : Base de données + schéma SQL
- **Membre 3** : Authentification + sessions

#### **Tâches en Parallèle (Sémaine 2)**
- **Membre 1** : Valve + annonces
- **Membre 2** : Upload fichiers + compression
- **Membre 3** : Sécurité + validation

### **PRIORITÉS ABSOLUES**
1. **Authentification fonctionnelle** (Jour 2)
2. **Hiérarchie POO correcte** (Jour 3)
3. **Messagerie basique** (Jour 4)
4. **Valve opérationnel** (Jour 6)
5. **Sécurité implémentée** (Jour 8)

### **FONCTIONNALITÉS MINIMALES POUR LA SOUTENANCE**
- ✅ Login/Logout pour les 6 rôles
- ✅ Envoi/réception de messages (règles de visibilité)
- ✅ Convocation par Doyen/Vice-Doyen
- ✅ Publication Valve par Apparitaire
- ✅ Upload de fichiers (20 Mo max)
- ✅ Tableaux de bord par rôle

### **CONSEILS POUR 2 SEMAINES**
1. **Travaillez en parallèle** dès le premier jour
2. **Priorisez les fonctionnalités core** (messagerie, auth)
3. **Utilisez votre frontend existant** comme base
4. **Testez au fur et à mesure** (pas à la fin)
5. **Simplifiez si nécessaire** (compression optionnelle)
6. **Réunissez-vous quotidiennement** pour synchroniser

### **CALENDRIER RÉCAPITULATIF**
**Lundi-Vendredi (Semaine 1)** : Infrastructure + Core
**Lundi-Mardi (Semaine 2)** : Fonctionnalités avancées
**Mercredi-Jeudi** : Sécurité + tests
**Vendredi** : Documentation + préparation soutenance

## 🛠️ Stack Technique
- **Backend** : PHP 8.x (POO natif)
- **Base de données** : MySQL/MariaDB avec PDO
- **Frontend** : HTML5, CSS3, JavaScript (vanilla)
- **Sécurité** : Sessions PHP, validation, PDO préparé
- **Compression** : GD Library pour images
- **Versioning** : Git/GitHub

## 📁 Structure des Fichiers
```
FasiChatClassRoom_PHP/
├── backend/           # Code PHP (POO)
├── database/          # Schémas et données SQL
├── frontend/          # Templates HTML/CSS/JS
├── public/            # Point d'entrée web
├── index.php          # Routeur principal
└── README.md          # Cette documentation
```

## 🔒 Exigences de Sécurité
- [ ] **PDO préparé** pour toutes les requêtes SQL
- [ ] **Validation** de toutes les entrées utilisateur
- [ ] **Échappement XSS** avec `htmlspecialchars()`
- [ ] **Gestion sécurisée** des sessions
- [ ] **Contrôle d'accès** basé sur les rôles
- [ ] **Validation des fichiers** (type, taille, contenu)

## 📊 Livrables Attendus
1. **Schéma de base de données** (`database/schema.sql`)
2. **Code source PHP** (dépôt GitHub)
3. **Rapport technique** (6-10 pages) justifiant les choix POO
4. **Fichier de démonstration** (`database/test_data.sql`)
5. **Présentation orale** lors de la soutenance

## 👥 Répartition Recommandée
### **Membre 1** (Expert POO/Architecture)
- Hiérarchie des classes utilisateurs
- Système de rôles et permissions
- Gestion du Valve et annonces
- Modélisation des relations

### **Membre 2** (Expert Base de Données/Fichiers)
- Schéma SQL et relations
- Implémentation PDO
- Système de fichiers et upload
- Compression d'images/vidéos

### **Membre 3** (Expert Sécurité/Intégration)
- Authentification et sessions
- Validation et sécurité
- Contrôleurs et routage
- Intégration frontend/backend

## ⚠️ Contraintes Importantes
- ❌ **Interdit** : Frameworks PHP (Laravel, Symfony, etc.)
- ✅ **Obligatoire** : Architecture POO pure
- ✅ **Obligatoire** : PDO avec requêtes préparées
- ✅ **Obligatoire** : 6 rôles avec comptes de test
- ✅ **Obligatoire** : Compression automatique des médias

## 🚀 Démarrage Rapide
1. **Cloner la structure** : `git clone [repository]`
2. **Configurer la base de données** : Exécuter `database/schema.sql`
3. **Peupler les données** : Exécuter `database/test_data.sql`
4. **Configurer** : Modifier `backend/config/Config.php`
5. **Démarrer** : Accéder à `index.php`

## 📞 Support & Collaboration
- **Réunions d'équipe** : 2 fois par semaine minimum
- **Versionning** : Commits réguliers sur GitHub
- **Communication** : Discord/Slack pour coordination
- **Revue de code** : Revue mutuelle avant intégration

---

**Date de début** : [À définir]  
**Date de fin** : [Selon consignes du professeur]  
**Statut** : 🟡 En cours de développement

*Cette roadmap sera mise à jour au fur et à mesure de l'avancement du projet.*