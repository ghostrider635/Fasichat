-- ============================================================
-- FasiChat Classroom - Base de donnees complete
-- ============================================================
-- Base: fasichat_classroom
-- Encodage: utf8mb4
--
-- Identifiants de demonstration:
--   Doyen                  | doyen@fasi.edu        | Doyen123!
--   Vice-Doyen             | vicedoyen@fasi.edu    | ViceDoyen123!
--   Administrateur         | admin@fasi.edu        | Admin123!
--   Apparitaire            | apparitaire@fasi.edu  | Apparitaire123!
--   Enseignant             | enseignant@fasi.edu   | Enseignant123!
--   Etudiant               | etudiant@fasi.edu     | Etudiant123!
--
-- Utilisation:
--   1. Importer ce fichier dans phpMyAdmin ou MySQL.
--   2. Verifier config/app.php:
--      host=127.0.0.1, name=fasichat_classroom, user=root, pass=votre_mot_de_passe
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS fasichat_classroom
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE fasichat_classroom;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS sessions_utilisateur;
DROP TABLE IF EXISTS demandes_administration;
DROP TABLE IF EXISTS annonces_valve;
DROP TABLE IF EXISTS valve;
DROP TABLE IF EXISTS publications_mur;
DROP TABLE IF EXISTS mur_pedagogique;
DROP TABLE IF EXISTS convocations_destinataires;
DROP TABLE IF EXISTS convocations;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS conversations;
DROP TABLE IF EXISTS fichiers;
DROP TABLE IF EXISTS etudiants_promotions;
DROP TABLE IF EXISTS enseignants_cours;
DROP TABLE IF EXISTS cours_promotions;
DROP TABLE IF EXISTS cours;
DROP TABLE IF EXISTS promotions;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. ROLES
-- ============================================================
CREATE TABLE roles (
    id INT AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    CONSTRAINT pk_roles PRIMARY KEY (id),
    CONSTRAINT uq_role_nom UNIQUE (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. UTILISATEURS
-- ============================================================
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_utilisateurs PRIMARY KEY (id),
    CONSTRAINT uq_utilisateur_email UNIQUE (email),
    CONSTRAINT fk_utilisateurs_roles FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. PROMOTIONS
-- ============================================================
CREATE TABLE promotions (
    id INT AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    CONSTRAINT pk_promotions PRIMARY KEY (id),
    CONSTRAINT uq_promotion_nom UNIQUE (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. COURS
-- ============================================================
CREATE TABLE cours (
    id INT AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    code VARCHAR(20) NOT NULL,
    CONSTRAINT pk_cours PRIMARY KEY (id),
    CONSTRAINT uq_cours_code UNIQUE (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. ENSEIGNANTS_COURS
-- ============================================================
CREATE TABLE enseignants_cours (
    enseignant_id INT NOT NULL,
    cours_id INT NOT NULL,
    CONSTRAINT pk_enseignants_cours PRIMARY KEY (enseignant_id, cours_id),
    CONSTRAINT fk_ens_cours_enseignant FOREIGN KEY (enseignant_id)
        REFERENCES utilisateurs(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_ens_cours_cours FOREIGN KEY (cours_id)
        REFERENCES cours(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. COURS_PROMOTIONS
-- ============================================================
CREATE TABLE cours_promotions (
    cours_id INT NOT NULL,
    promotion_id INT NOT NULL,
    CONSTRAINT pk_cours_promotions PRIMARY KEY (cours_id, promotion_id),
    CONSTRAINT fk_cours_prom_cours FOREIGN KEY (cours_id)
        REFERENCES cours(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_cours_prom_promotion FOREIGN KEY (promotion_id)
        REFERENCES promotions(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. ETUDIANTS_PROMOTIONS
-- ============================================================
CREATE TABLE etudiants_promotions (
    etudiant_id INT NOT NULL,
    promotion_id INT NOT NULL,
    CONSTRAINT pk_etudiants_promotions PRIMARY KEY (etudiant_id, promotion_id),
    CONSTRAINT fk_etud_prom_etudiant FOREIGN KEY (etudiant_id)
        REFERENCES utilisateurs(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_etud_prom_promotion FOREIGN KEY (promotion_id)
        REFERENCES promotions(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. FICHIERS
-- ============================================================
CREATE TABLE fichiers (
    id INT AUTO_INCREMENT,
    nom_origine VARCHAR(255) NOT NULL,
    nom_stockage VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    taille INT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_fichiers PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. CONVERSATIONS
-- ============================================================
CREATE TABLE conversations (
    id INT AUTO_INCREMENT,
    type ENUM('prive', 'public_promotion', 'confidentiel') NOT NULL,
    promotion_id INT NULL,
    expediteur_id INT NULL,
    destinataire_id INT NULL,
    CONSTRAINT pk_conversations PRIMARY KEY (id),
    CONSTRAINT fk_conversations_promotions FOREIGN KEY (promotion_id)
        REFERENCES promotions(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_conversations_expediteur FOREIGN KEY (expediteur_id)
        REFERENCES utilisateurs(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_conversations_destinataire FOREIGN KEY (destinataire_id)
        REFERENCES utilisateurs(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. MESSAGES
-- ============================================================
CREATE TABLE messages (
    id INT AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    expediteur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    fichier_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_messages PRIMARY KEY (id),
    CONSTRAINT fk_messages_conversations FOREIGN KEY (conversation_id)
        REFERENCES conversations(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_messages_expediteur FOREIGN KEY (expediteur_id)
        REFERENCES utilisateurs(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_messages_fichiers FOREIGN KEY (fichier_id)
        REFERENCES fichiers(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. CONVOCATIONS
-- ============================================================
CREATE TABLE convocations (
    id INT AUTO_INCREMENT,
    expediteur_id INT NOT NULL,
    objet VARCHAR(255) NOT NULL,
    date_convocation DATE NOT NULL,
    heure_convocation TIME NOT NULL,
    lieu VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_convocations PRIMARY KEY (id),
    CONSTRAINT fk_convocations_expediteur FOREIGN KEY (expediteur_id)
        REFERENCES utilisateurs(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. CONVOCATIONS_DESTINATAIRES
-- ============================================================
CREATE TABLE convocations_destinataires (
    convocation_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    lu TINYINT(1) DEFAULT 0,
    CONSTRAINT pk_convocations_destinataires PRIMARY KEY (convocation_id, destinataire_id),
    CONSTRAINT fk_conv_dest_convocation FOREIGN KEY (convocation_id)
        REFERENCES convocations(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_conv_dest_destinataire FOREIGN KEY (destinataire_id)
        REFERENCES utilisateurs(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. MUR_PEDAGOGIQUE
-- ============================================================
CREATE TABLE mur_pedagogique (
    id INT AUTO_INCREMENT,
    cours_id INT NOT NULL,
    CONSTRAINT pk_mur_pedagogique PRIMARY KEY (id),
    CONSTRAINT uq_mur_cours UNIQUE (cours_id),
    CONSTRAINT fk_mur_cours FOREIGN KEY (cours_id)
        REFERENCES cours(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. PUBLICATIONS_MUR
-- ============================================================
CREATE TABLE publications_mur (
    id INT AUTO_INCREMENT,
    mur_id INT NOT NULL,
    auteur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    fichier_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_publications_mur PRIMARY KEY (id),
    CONSTRAINT fk_pub_mur_base FOREIGN KEY (mur_id)
        REFERENCES mur_pedagogique(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_pub_mur_auteur FOREIGN KEY (auteur_id)
        REFERENCES utilisateurs(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_pub_mur_fichier FOREIGN KEY (fichier_id)
        REFERENCES fichiers(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. VALVE
-- ============================================================
CREATE TABLE valve (
    id INT AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    CONSTRAINT pk_valve PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. ANNONCES_VALVE
-- ============================================================
CREATE TABLE annonces_valve (
    id INT AUTO_INCREMENT,
    valve_id INT NOT NULL,
    auteur_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    categorie VARCHAR(100) NOT NULL DEFAULT 'Information',
    fichier_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_annonces_valve PRIMARY KEY (id),
    CONSTRAINT fk_annonces_valve_base FOREIGN KEY (valve_id)
        REFERENCES valve(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_annonces_valve_auteur FOREIGN KEY (auteur_id)
        REFERENCES utilisateurs(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_annonces_valve_fichier FOREIGN KEY (fichier_id)
        REFERENCES fichiers(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. SESSIONS_UTILISATEUR
-- ============================================================
CREATE TABLE sessions_utilisateur (
    id INT AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NOT NULL,
    expire_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_sessions_utilisateur PRIMARY KEY (id),
    CONSTRAINT uq_session_token UNIQUE (token),
    CONSTRAINT fk_sessions_utilisateurs FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateurs(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. DEMANDES_ADMINISTRATION
-- ============================================================
CREATE TABLE demandes_administration (
    id INT AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    role_demande VARCHAR(80) NOT NULL,
    message TEXT NOT NULL,
    statut VARCHAR(30) NOT NULL DEFAULT 'nouvelle',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_demandes_administration PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INDEX OPTIMISATION
-- ============================================================
CREATE INDEX idx_utilisateurs_email ON utilisateurs(email);
CREATE INDEX idx_messages_conversation ON messages(conversation_id);
CREATE INDEX idx_annonces_valve_date ON annonces_valve(created_at DESC);
CREATE INDEX idx_publications_mur_date ON publications_mur(created_at DESC);

-- ============================================================
-- DONNEES DE DEMONSTRATION
-- ============================================================

INSERT INTO roles (id, nom) VALUES
(1, 'Doyen'),
(2, 'Vice-Doyen'),
(3, 'Administrateur Academique'),
(4, 'Apparitaire'),
(5, 'Enseignant'),
(6, 'Etudiant');

INSERT INTO promotions (id, nom) VALUES
(1, 'L1 Informatique'),
(2, 'L2 Informatique'),
(3, 'L3 Informatique');

INSERT INTO cours (id, nom, code) VALUES
(1, 'Programmation Web', 'WEB101'),
(2, 'Base de Donnees', 'BDD202'),
(3, 'Algorithmique', 'ALG303');

INSERT INTO utilisateurs (id, nom, prenom, email, mot_de_passe, role_id) VALUES
(1, 'Diop', 'Mamadou', 'doyen@fasi.edu', '$2y$12$285OWHV330FShKQ5M6OMSOKTKEGjqgAKGEUgQOe5UORx25qb0kAEK', 1),
(2, 'Ndiaye', 'Aminata', 'vicedoyen@fasi.edu', '$2y$12$RHzBOlm5LFU7ZOG6O4XhA.o/ECUNW85qi8sxw28BP/M2KHU0vTuC6', 2),
(3, 'Fall', 'Awa', 'admin@fasi.edu', '$2y$10$wRLxGFS9elTSEG9xhZWdPOq76p67PVpSKtqatmWmr7dzvZXOh0PZC', 3),
(4, 'Sow', 'Ibrahima', 'apparitaire@fasi.edu', '$2y$10$V7.k/aXrFKMJq8s09i0QMOlVlIVTCsuW4Sm1TrZFY3bmbAvuFjuPy', 4),
(5, 'Coulibaly', 'Seynabou', 'enseignant@fasi.edu', '$2y$10$w4tpSqlFUZNnOJwd24SKZul8fb9jVDwvLxwLSQWnMm0y20D3VGP/i', 5),
(6, 'Sarr', 'Fatou', 'etudiant@fasi.edu', '$2y$12$MQ8Q4TQrt0yrN5FDxBHrq.1nsfjXvM9fE1HRBgUck1OuO2choZe.q', 6);

INSERT INTO enseignants_cours (enseignant_id, cours_id) VALUES
(5, 1),
(5, 2);

INSERT INTO cours_promotions (cours_id, promotion_id) VALUES
(1, 2),
(2, 2),
(3, 1);

INSERT INTO etudiants_promotions (etudiant_id, promotion_id) VALUES
(6, 2);

INSERT INTO valve (id, nom) VALUES
(1, 'Panneau FasiChat');

INSERT INTO annonces_valve (id, valve_id, auteur_id, titre, contenu, categorie) VALUES
(1, 1, 4, 'Bienvenue sur FasiChat', 'La plateforme est operationnelle pour les enseignants et les etudiants.', 'Information'),
(2, 1, 4, 'Depot des projets', 'Le depot des projets se fera via la plateforme FasiChat avant la date limite.', 'Academique');

INSERT INTO convocations (id, expediteur_id, objet, date_convocation, heure_convocation, lieu, message) VALUES
(1, 1, 'Reunion de coordination', '2026-06-15', '10:00:00', 'Salle B101', 'Veuillez assister a la reunion de coordination pedagogique.');

INSERT INTO convocations_destinataires (convocation_id, destinataire_id) VALUES
(1, 5),
(1, 6);

INSERT INTO mur_pedagogique (id, cours_id) VALUES
(1, 1),
(2, 2);

INSERT INTO publications_mur (id, mur_id, auteur_id, contenu) VALUES
(1, 1, 5, 'Bienvenue sur le mur pedagogique du cours Programmation Web.'),
(2, 2, 5, 'Rappel: revise les requetes SQL et les jointures.');

INSERT INTO conversations (id, type, promotion_id, expediteur_id, destinataire_id) VALUES
(1, 'prive', NULL, 6, 5),
(2, 'confidentiel', NULL, 1, 2);

INSERT INTO messages (id, conversation_id, expediteur_id, contenu) VALUES
(1, 1, 6, 'Bonjour professeur, pouvez-vous confirmer la date de remise du projet ?'),
(2, 1, 5, 'Bonjour, la remise est prevue pour vendredi avant 23h59.'),
(3, 2, 1, 'Bonjour, preparons la reunion de coordination de la semaine prochaine.');

-- ============================================================
-- RAPPEL DES IDENTIFIANTS DE CONNEXION
-- ============================================================
-- Doyen                  : doyen@fasi.edu       / Doyen123!
-- Vice-Doyen             : vicedoyen@fasi.edu   / ViceDoyen123!
-- Administrateur         : admin@fasi.edu       / Admin123!
-- Apparitaire            : apparitaire@fasi.edu / Apparitaire123!
-- Enseignant             : enseignant@fasi.edu  / Enseignant123!
-- Etudiant               : etudiant@fasi.edu    / Etudiant123!
