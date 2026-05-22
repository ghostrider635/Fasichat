# Installation de FasiChat Classroom

## Prérequis

- PHP 8.x ou supérieur
- MySQL/MariaDB
- Serveur web (Apache recommandé avec XAMPP/WAMP)
- Composer (pour les dépendances)

## Installation étape par étape

### 1. Configuration de la base de données

#### Option A : Via phpMyAdmin (recommandé)
1. Ouvrir phpMyAdmin
2. Créer une nouvelle base de données nommée `fasichat_db`
3. Sélectionner cette base de données
4. Cliquer sur l'onglet "SQL"
5. Copier-coller le contenu de `database/schema.sql`
6. Exécuter le script

#### Option B : Via ligne de commande MySQL
```bash
mysql -u root -p
CREATE DATABASE fasichat_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fasichat_db;
SOURCE database/schema.sql;
```

### 2. Peuplement des données de test
```bash
mysql -u root -p fasichat_db < database/test_data.sql
```

### 3. Configuration de l'application

#### Modifier la configuration de la base de données
Ouvrir le fichier `backend/config/Config.php` et vérifier/modifier :
```php
const DB_HOST = 'localhost';
const DB_NAME = 'fasichat_db';
const DB_USER = 'root';
const DB_PASS = ''; // Votre mot de passe MySQL
```

### 4. Configuration du serveur web

#### Pour XAMPP/WAMP :
1. Copier le dossier `Fasichat` dans `htdocs` (XAMPP) ou `www` (WAMP)
2. Accéder à `http://localhost/Fasichat/`

#### Configuration Apache (.htaccess)
Le fichier `.htaccess` est déjà configuré pour :
- Rediriger toutes les requêtes vers `index.php`
- Activer la réécriture d'URL
- Protéger les fichiers sensibles

### 5. Installation des dépendances (si nécessaire)
```bash
composer install
```

## Vérification de l'installation

### 1. Vérifier la base de données
Connectez-vous à MySQL et exécutez :
```sql
USE fasichat_db;
SHOW TABLES;
SELECT COUNT(*) as total FROM utilisateurs;
```

Vous devriez voir :
- 18 tables créées
- 12 utilisateurs (6 rôles différents)

### 2. Tester l'application
Accédez à `http://localhost/Fasichat/` et connectez-vous avec :

#### Comptes de test disponibles :
| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Étudiant | jean.dupont@etudiant.univ.fr | password123 |
| Enseignant | philippe.leroy@univ.fr | password123 |
| Assistant | nicolas.laurent@univ.fr | password123 |
| Doyen | robert.michel@univ.fr | password123 |
| Vice-Doyen | claire.garcia@univ.fr | password123 |
| Apparitaire | david.roux@univ.fr | password123 |

## Structure de la base de données

### Tables principales :
1. **utilisateurs** - Table mère de tous les utilisateurs
2. **etudiants**, **enseignants**, etc. - Tables spécialisées par rôle
3. **messages** - Tous les types de messages
4. **convocations** - Messages de type convocation
5. **annonces_valve** - Annonces institutionnelles
6. **fichiers** - Fichiers uploadés
7. **cours** et **promotions** - Gestion académique

### Relations clés :
- Héritage : Chaque table spécialisée référence `utilisateurs.id_utilisateur`
- Messages : Polymorphisme via `type_message`
- Convocations : Association many-to-many avec `participants_convocations`

## Dépannage

### Problème : Erreur de connexion à la base de données
**Solution :** Vérifier les identifiants dans `Config.php`

### Problème : Tables non créées
**Solution :** Exécuter manuellement `schema.sql` dans phpMyAdmin

### Problème : Pages blanches
**Solution :** Activer l'affichage des erreurs dans PHP :
```php
// Dans index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Problème : Upload de fichiers impossible
**Solution :** Vérifier les permissions sur le dossier `public/uploads/`

## Scripts SQL utiles

### Réinitialiser la base de données :
```bash
mysql -u root -p fasichat_db < database/schema.sql
mysql -u root -p fasichat_db < database/test_data.sql
```

### Voir toutes les données :
```sql
-- Voir tous les utilisateurs avec leurs rôles
SELECT * FROM v_utilisateurs_complets;

-- Voir tous les messages détaillés
SELECT * FROM v_messages_detaille;

-- Voir les convocations avec participants
SELECT * FROM v_convocations_participants;
```

### Procédures stockées disponibles :
```sql
-- Archiver les anciens messages
CALL archiver_messages_anciens();

-- Créer un nouvel utilisateur
CALL creer_utilisateur('Nom', 'Prénom', 'email@test.fr', 'motdepasse', 'etudiant', 'ETU2025006', 1);
```

## Sécurité

### Changer les mots de passe par défaut :
Dans `test_data.sql`, remplacer les hashs par des vrais hashs :
```php
// Générer un nouveau hash
$hash = password_hash('nouveaumotdepasse', PASSWORD_BCRYPT, ['cost' => 12]);
```

### Configuration de production :
1. Changer `APP_ENV` à `production` dans `Config.php`
2. Modifier les identifiants de la base de données
3. Désactiver l'affichage des erreurs
4. Configurer HTTPS

## Support

Pour toute question ou problème :
1. Consulter les logs dans `logs/` (si activés)
2. Vérifier les permissions des fichiers
3. Tester la connexion MySQL directement
4. Vérifier la configuration PHP (php.ini)

## Notes importantes

- Le schéma utilise `utf8mb4` pour supporter tous les caractères Unicode
- Les contraintes d'intégrité sont activées (clés étrangères, triggers)
- Les index sont optimisés pour les requêtes fréquentes
- Les vues simplifient les requêtes complexes
- Les procédures stockées automatisent les tâches courantes

---

**Statut de l'installation :** ✅ Prêt à l'emploi  
**Dernière vérification :** 21/05/2026  
**Version du schéma :** 1.0