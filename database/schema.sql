-- ============================================
-- SCHEMA SQL COMPLET - FASICHAT CLASSROOM
-- Base de données MySQL/MariaDB
-- Version: 1.0
-- Date: 21/05/2026
-- ============================================

-- Suppression des tables existantes (dans l'ordre inverse des dépendances)
DROP TABLE IF EXISTS fichiers_messages;
DROP TABLE IF EXISTS participants_convocations;
DROP TABLE IF EXISTS etudiants_cours;
DROP TABLE IF EXISTS annonces_valve;
DROP TABLE IF EXISTS mur_pedagogique;
DROP TABLE IF EXISTS fichiers;
DROP TABLE IF EXISTS messages_prives;
DROP TABLE IF EXISTS messages_publics;
DROP TABLE IF EXISTS convocations;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS etudiants;
DROP TABLE IF EXISTS enseignants;
DROP TABLE IF EXISTS assistants;
DROP TABLE IF EXISTS doyens;
DROP TABLE IF EXISTS vice_doyens;
DROP TABLE IF EXISTS apparitaires;
DROP TABLE IF EXISTS cours;
DROP TABLE IF EXISTS promotions;
DROP TABLE IF EXISTS utilisateurs;

-- ============================================
-- CRÉATION DES TABLES
-- ============================================

-- Table principale des utilisateurs
CREATE TABLE utilisateurs (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    type_utilisateur ENUM('etudiant', 'enseignant', 'assistant', 'doyen', 'vice_doyen', 'apparitaire') NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des promotions
CREATE TABLE promotions (
    id_promotion INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    annee YEAR NOT NULL,
    niveau VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des étudiants
CREATE TABLE etudiants (
    id_etudiant INT PRIMARY KEY,
    numero_etudiant VARCHAR(20) UNIQUE NOT NULL,
    promotion_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_etudiant) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (promotion_id) REFERENCES promotions(id_promotion) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des enseignants
CREATE TABLE enseignants (
    id_enseignant INT PRIMARY KEY,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    specialite VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_enseignant) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des assistants
CREATE TABLE assistants (
    id_assistant INT PRIMARY KEY,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    enseignant_referent_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_assistant) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (enseignant_referent_id) REFERENCES enseignants(id_enseignant) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des doyens
CREATE TABLE doyens (
    id_doyen INT PRIMARY KEY,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_doyen) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des vice-doyens
CREATE TABLE vice_doyens (
    id_vice_doyen INT PRIMARY KEY,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vice_doyen) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des apparitaires
CREATE TABLE apparitaires (
    id_apparitaire INT PRIMARY KEY,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_apparitaire) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des cours
CREATE TABLE cours (
    id_cours INT PRIMARY KEY AUTO_INCREMENT,
    intitule VARCHAR(200) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    enseignant_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (enseignant_id) REFERENCES enseignants(id_enseignant) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table d'association étudiants-cours
CREATE TABLE etudiants_cours (
    etudiant_id INT,
    cours_id INT,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    note_finale DECIMAL(4,2) DEFAULT NULL,
    statut ENUM('inscrit', 'abandon', 'termine') DEFAULT 'inscrit',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (etudiant_id, cours_id),
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id_etudiant) ON DELETE CASCADE,
    FOREIGN KEY (cours_id) REFERENCES cours(id_cours) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table principale des messages
CREATE TABLE messages (
    id_message INT PRIMARY KEY AUTO_INCREMENT,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    expediteur_id INT NOT NULL,
    type_message ENUM('prive', 'public', 'convocation') NOT NULL,
    statut ENUM('envoye', 'lu', 'supprime') DEFAULT 'envoye',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des messages privés
CREATE TABLE messages_prives (
    id_message_prive INT PRIMARY KEY,
    destinataire_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_message_prive) REFERENCES messages(id_message) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des messages publics
CREATE TABLE messages_publics (
    id_message_public INT PRIMARY KEY,
    cours_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_message_public) REFERENCES messages(id_message) ON DELETE CASCADE,
    FOREIGN KEY (cours_id) REFERENCES cours(id_cours) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des convocations
CREATE TABLE convocations (
    id_convocation INT PRIMARY KEY,
    date_reunion DATETIME NOT NULL,
    lieu VARCHAR(200) NOT NULL,
    objet TEXT,
    duree_estimee INT COMMENT 'Durée en minutes',
    statut ENUM('planifiee', 'annulee', 'terminee') DEFAULT 'planifiee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_convocation) REFERENCES messages(id_message) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table d'association utilisateurs-convocations
CREATE TABLE participants_convocations (
    utilisateur_id INT,
    convocation_id INT,
    statut_presence ENUM('present', 'absent', 'en_attente') DEFAULT 'en_attente',
    date_confirmation DATETIME DEFAULT NULL,
    commentaire TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (utilisateur_id, convocation_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (convocation_id) REFERENCES convocations(id_convocation) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des fichiers
CREATE TABLE fichiers (
    id_fichier INT PRIMARY KEY AUTO_INCREMENT,
    nom_original VARCHAR(255) NOT NULL,
    nom_serveur VARCHAR(255) UNIQUE NOT NULL,
    chemin VARCHAR(500) NOT NULL,
    type_mime VARCHAR(100),
    taille BIGINT NOT NULL,
    date_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    uploader_id INT NOT NULL,
    est_compresse BOOLEAN DEFAULT FALSE,
    taille_originale BIGINT DEFAULT NULL,
    statut ENUM('actif', 'supprime') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uploader_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table d'association fichiers-messages
CREATE TABLE fichiers_messages (
    fichier_id INT,
    message_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (fichier_id, message_id),
    FOREIGN KEY (fichier_id) REFERENCES fichiers(id_fichier) ON DELETE CASCADE,
    FOREIGN KEY (message_id) REFERENCES messages(id_message) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des annonces valve
CREATE TABLE annonces_valve (
    id_annonce INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(200) NOT NULL,
    contenu TEXT NOT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    apparitaire_id INT NOT NULL,
    statut ENUM('publiee', 'brouillon', 'archivee') DEFAULT 'brouillon',
    date_expiration DATETIME DEFAULT NULL,
    priorite ENUM('normale', 'urgente', 'information') DEFAULT 'normale',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (apparitaire_id) REFERENCES apparitaires(id_apparitaire) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table du mur pédagogique
CREATE TABLE mur_pedagogique (
    id_mur INT PRIMARY KEY AUTO_INCREMENT,
    cours_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    enseignant_id INT NOT NULL,
    type_contenu ENUM('annonce', 'ressource', 'devoir', 'information') DEFAULT 'annonce',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cours_id) REFERENCES cours(id_cours) ON DELETE CASCADE,
    FOREIGN KEY (enseignant_id) REFERENCES enseignants(id_enseignant) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INDEX POUR LES PERFORMANCES
-- ============================================

-- Index pour les utilisateurs
CREATE INDEX idx_utilisateurs_email ON utilisateurs(email);
CREATE INDEX idx_utilisateurs_type ON utilisateurs(type_utilisateur);
CREATE INDEX idx_utilisateurs_statut ON utilisateurs(statut);

-- Index pour les étudiants
CREATE INDEX idx_etudiants_promotion ON etudiants(promotion_id);
CREATE INDEX idx_etudiants_numero ON etudiants(numero_etudiant);

-- Index pour les messages
CREATE INDEX idx_messages_expediteur ON messages(expediteur_id);
CREATE INDEX idx_messages_date ON messages(date_envoi);
CREATE INDEX idx_messages_type ON messages(type_message);
CREATE INDEX idx_messages_statut ON messages(statut);

-- Index pour les messages privés
CREATE INDEX idx_messages_prives_destinataire ON messages_prives(destinataire_id);

-- Index pour les messages publics
CREATE INDEX idx_messages_publics_cours ON messages_publics(cours_id);

-- Index pour les convocations
CREATE INDEX idx_convocations_date ON convocations(date_reunion);
CREATE INDEX idx_convocations_statut ON convocations(statut);

-- Index pour les participants convocations
CREATE INDEX idx_participants_convocation ON participants_convocations(convocation_id);
CREATE INDEX idx_participants_statut ON participants_convocations(statut_presence);

-- Index pour les cours
CREATE INDEX idx_cours_enseignant ON cours(enseignant_id);
CREATE INDEX idx_cours_code ON cours(code);

-- Index pour les étudiants-cours
CREATE INDEX idx_etudiants_cours_cours ON etudiants_cours(cours_id);
CREATE INDEX idx_etudiants_cours_statut ON etudiants_cours(statut);

-- Index pour les fichiers
CREATE INDEX idx_fichiers_uploader ON fichiers(uploader_id);
CREATE INDEX idx_fichiers_date ON fichiers(date_upload);
CREATE INDEX idx_fichiers_type ON fichiers(type_mime);

-- Index pour les annonces valve
CREATE INDEX idx_annonces_valve_date ON annonces_valve(date_publication);
CREATE INDEX idx_annonces_valve_statut ON annonces_valve(statut);
CREATE INDEX idx_annonces_valve_apparitaire ON annonces_valve(apparitaire_id);

-- Index pour le mur pédagogique
CREATE INDEX idx_mur_cours ON mur_pedagogique(cours_id);
CREATE INDEX idx_mur_enseignant ON mur_pedagogique(enseignant_id);
CREATE INDEX idx_mur_date ON mur_pedagogique(date_publication);

-- ============================================
-- CONTRAINTES D'INTÉGRITÉ
-- ============================================

-- Contrainte pour vérifier que le contenu du message n'est pas vide
ALTER TABLE messages ADD CONSTRAINT chk_contenu_non_vide 
CHECK (LENGTH(TRIM(contenu)) > 0);

-- Contrainte pour limiter la taille des fichiers à 20 Mo
ALTER TABLE fichiers ADD CONSTRAINT chk_taille_max 
CHECK (taille <= 20971520);

-- Contrainte pour valider le format d'email
ALTER TABLE utilisateurs ADD CONSTRAINT chk_email_valide 
CHECK (email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$');

-- Contrainte pour les dates de convocation (doit être dans le futur lors de la création)
DELIMITER //
CREATE TRIGGER before_insert_convocations
BEFORE INSERT ON convocations
FOR EACH ROW
BEGIN
    IF NEW.date_reunion <= NOW() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La date de réunion doit être dans le futur';
    END IF;
END//
DELIMITER ;

-- Contrainte pour empêcher un utilisateur d'être dans plusieurs tables spécialisées
DELIMITER //
CREATE TRIGGER before_insert_utilisateur_specialise
BEFORE INSERT ON etudiants
FOR EACH ROW
BEGIN
    DECLARE user_type VARCHAR(20);
    SELECT type_utilisateur INTO user_type FROM utilisateurs WHERE id_utilisateur = NEW.id_etudiant;
    
    IF user_type != 'etudiant' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'L\'utilisateur doit être de type étudiant';
    END IF;
END//
DELIMITER ;

-- Même contrainte pour les autres tables spécialisées
DELIMITER //
CREATE TRIGGER before_insert_enseignant_specialise
BEFORE INSERT ON enseignants
FOR EACH ROW
BEGIN
    DECLARE user_type VARCHAR(20);
    SELECT type_utilisateur INTO user_type FROM utilisateurs WHERE id_utilisateur = NEW.id_enseignant;
    
    IF user_type != 'enseignant' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'L\'utilisateur doit être de type enseignant';
    END IF;
END//
DELIMITER ;

-- ============================================
-- VUES UTILES
-- ============================================

-- Vue pour afficher tous les utilisateurs avec leurs détails
CREATE VIEW v_utilisateurs_complets AS
SELECT 
    u.id_utilisateur,
    u.nom,
    u.prenom,
    u.email,
    u.type_utilisateur,
    u.date_inscription,
    u.statut,
    CASE 
        WHEN u.type_utilisateur = 'etudiant' THEN e.numero_etudiant
        WHEN u.type_utilisateur = 'enseignant' THEN en.matricule
        WHEN u.type_utilisateur = 'assistant' THEN a.matricule
        WHEN u.type_utilisateur = 'doyen' THEN d.matricule
        WHEN u.type_utilisateur = 'vice_doyen' THEN vd.matricule
        WHEN u.type_utilisateur = 'apparitaire' THEN ap.matricule
    END AS matricule_numero,
    CASE 
        WHEN u.type_utilisateur = 'etudiant' THEN p.nom
        ELSE NULL
    END AS promotion_nom
FROM utilisateurs u
LEFT JOIN etudiants e ON u.id_utilisateur = e.id_etudiant
LEFT JOIN enseignants en ON u.id_utilisateur = en.id_enseignant
LEFT JOIN assistants a ON u.id_utilisateur = a.id_assistant
LEFT JOIN doyens d ON u.id_utilisateur = d.id_doyen
LEFT JOIN vice_doyens vd ON u.id_utilisateur = vd.id_vice_doyen
LEFT JOIN apparitaires ap ON u.id_utilisateur = ap.id_apparitaire
LEFT JOIN promotions p ON e.promotion_id = p.id_promotion;

-- Vue pour les messages avec détails
CREATE VIEW v_messages_detaille AS
SELECT 
    m.id_message,
    m.contenu,
    m.date_envoi,
    m.type_message,
    m.statut,
    u_exp.nom AS expediteur_nom,
    u_exp.prenom AS expediteur_prenom,
    u_exp.type_utilisateur AS expediteur_type,
    CASE 
        WHEN m.type_message = 'prive' THEN 
            (SELECT CONCAT(u_dest.nom, ' ', u_dest.prenom) 
             FROM messages_prives mp 
             JOIN utilisateurs u_dest ON mp.destinataire_id = u_dest.id_utilisateur
             WHERE mp.id_message_prive = m.id_message)
        WHEN m.type_message = 'public' THEN 
            (SELECT c.intitule 
             FROM messages_publics mpub 
             JOIN cours c ON mpub.cours_id = c.id_cours
             WHERE mpub.id_message_public = m.id_message)
        WHEN m.type_message = 'convocation' THEN 
            (SELECT CONCAT('Convocation - ', c.lieu, ' - ', c.date_reunion)
             FROM convocations c 
             WHERE c.id_convocation = m.id_message)
    END AS destinataire_info
FROM messages m
JOIN utilisateurs u_exp ON m.expediteur_id = u_exp.id_utilisateur;

-- Vue pour les convocations avec participants
CREATE VIEW v_convocations_participants AS
SELECT 
    c.id_convocation,
    m.contenu AS objet,
    c.date_reunion,
    c.lieu,
    c.statut AS statut_convocation,
    u_exp.nom AS organisateur_nom,
    u_exp.prenom AS organisateur_prenom,
    COUNT(pc.utilisateur_id) AS nombre_participants,
    SUM(CASE WHEN pc.statut_presence = 'present' THEN 1 ELSE 0 END) AS confirmes_presents,
    SUM(CASE WHEN pc.statut_presence = 'absent' THEN 1 ELSE 0 END) AS confirmes_absents
FROM convocations c
JOIN messages m ON c.id_convocation = m.id_message
JOIN utilisateurs u_exp ON m.expediteur_id = u_exp.id_utilisateur
LEFT JOIN participants_convocations pc ON c.id_convocation = pc.convocation_id
GROUP BY c.id_convocation, m.contenu, c.date_reunion, c.lieu, c.statut, u_exp.nom, u_exp.prenom;

-- ============================================
-- PROCÉDURES STOCKÉES UTILES
-- ============================================

-- Procédure pour archiver les anciens messages
DELIMITER //
CREATE PROCEDURE archiver_messages_anciens()
BEGIN
    -- Marquer comme supprimés les messages de plus d'un an
    UPDATE messages 
    SET statut = 'supprime'
    WHERE date_envoi < DATE_SUB(NOW(), INTERVAL 1 YEAR)
    AND statut != 'supprime';
    
    -- Supprimer les fichiers orphelins de plus de 30 jours
    DELETE FROM fichiers 
    WHERE statut = 'actif'
    AND date_upload < DATE_SUB(NOW(), INTERVAL 30 DAY)
    AND id_fichier NOT IN (SELECT fichier_id FROM fichiers_messages);
    
    SELECT 'Archivage terminé' AS resultat;
END//
DELIMITER ;

-- Procédure pour créer un nouvel utilisateur
DELIMITER //
CREATE PROCEDURE creer_utilisateur(
    IN p_nom VARCHAR(100),
    IN p_prenom VARCHAR(100),
    IN p_email VARCHAR(255),
    IN p_mot_de_passe VARCHAR(255),
    IN p_type_utilisateur VARCHAR(20),
    IN p_matricule VARCHAR(20),
    IN p_promotion_id INT
)
BEGIN
    DECLARE v_user_id INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Insérer dans la table utilisateurs
    INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe_hash, type_utilisateur)
    VALUES (p_nom, p_prenom, p_email, p_mot_de_passe, p_type_utilisateur);
    
    SET v_user_id = LAST_INSERT_ID();
    
    -- Insérer dans la table spécialisée selon le type
    CASE p_type_utilisateur
        WHEN 'etudiant' THEN
            INSERT INTO etudiants (id_etudiant, numero_etudiant, promotion_id)
            VALUES (v_user_id, p_matricule, p_promotion_id);
        WHEN 'enseignant' THEN
            INSERT INTO enseignants (id_enseignant, matricule)
            VALUES (v_user_id, p_matricule);
        WHEN 'assistant' THEN
            INSERT INTO assistants (id_assistant, matricule)
            VALUES (v_user_id, p_matricule);
        WHEN 'doyen' THEN
            INSERT INTO doyens (id_doyen, matricule)
            VALUES (v_user_id, p_matricule);
        WHEN 'vice_doyen' THEN
            INSERT INTO vice_doyens (id_vice_doyen, matricule)
            VALUES (v_user_id, p_matricule);
        WHEN 'apparitaire' THEN
            INSERT INTO apparitaires (id_apparitaire, matricule)
            VALUES (v_user_id, p_matricule);
    END CASE;
    
    COMMIT;
    
    SELECT v_user_id AS nouvel_utilisateur_id;
END//
DELIMITER ;

-- ============================================
-- COMMENTAIRES ET DOCUMENTATION
-- ============================================

-- Commentaires sur les tables
ALTER TABLE utilisateurs COMMENT = 'Table principale des utilisateurs du système';
ALTER TABLE etudiants COMMENT = 'Table des étudiants (spécialisation de utilisateurs)';
ALTER TABLE enseignants COMMENT = 'Table des enseignants (spécialisation de utilisateurs)';
ALTER TABLE assistants COMMENT = 'Table des assistants (spécialisation de utilisateurs)';
ALTER TABLE doyens COMMENT = 'Table des doyens (spécialisation de utilisateurs)';
ALTER TABLE vice_doyens COMMENT = 'Table des vice-doyens (spécialisation de utilisateurs)';
ALTER TABLE apparitaires COMMENT = 'Table des apparitaires (spécialisation de utilisateurs)';
ALTER TABLE promotions COMMENT = 'Table des promotions/groups d\'étudiants';
ALTER TABLE cours COMMENT = 'Table des cours enseignés';
ALTER TABLE etudiants_cours COMMENT = 'Table d\'association étudiants-cours';
ALTER TABLE messages COMMENT = 'Table principale des messages';
ALTER TABLE messages_prives COMMENT = 'Table des messages privés (spécialisation de messages)';
ALTER TABLE messages_publics COMMENT = 'Table des messages publics (spécialisation de messages)';
ALTER TABLE convocations COMMENT = 'Table des convocations (spécialisation de messages)';
ALTER TABLE participants_convocations COMMENT = 'Table d\'association utilisateurs-convocations';
ALTER TABLE fichiers COMMENT = 'Table des fichiers uploadés';
ALTER TABLE fichiers_messages COMMENT = 'Table d\'association fichiers-messages';
ALTER TABLE annonces_valve COMMENT = 'Table des annonces du valve (réservé aux apparitaires)';
ALTER TABLE mur_pedagogique COMMENT = 'Table du mur pédagogique des cours';

-- ============================================
-- FIN DU SCRIPT
-- ============================================

SELECT 'Schéma de base de données FasiChat Classroom créé avec succès!' AS message;