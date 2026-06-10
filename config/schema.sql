CREATE DATABASE IF NOT EXISTS fasichat_classroom DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fasichat_classroom;

-- 1. ROLES
CREATE TABLE roles (
    id INT AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    CONSTRAINT pk_roles PRIMARY KEY (id),
    CONSTRAINT uq_role_nom UNIQUE (nom)
) ENGINE=InnoDB;

-- 2. UTILISATEURS
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
    CONSTRAINT fk_utilisateurs_roles FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. PROMOTIONS
CREATE TABLE promotions (
    id INT AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    CONSTRAINT pk_promotions PRIMARY KEY (id),
    CONSTRAINT uq_promotion_nom UNIQUE (nom)
) ENGINE=InnoDB;

-- 4. COURS
CREATE TABLE cours (
    id INT AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    code VARCHAR(20) NOT NULL,
    CONSTRAINT pk_cours PRIMARY KEY (id),
    CONSTRAINT uq_cours_code UNIQUE (code)
) ENGINE=InnoDB;

-- 5. ENSEIGNANTS_COURS (Table de jointure)
CREATE TABLE enseignants_cours (
    enseignant_id INT,
    cours_id INT,
    CONSTRAINT pk_enseignants_cours PRIMARY KEY (enseignant_id, cours_id),
    CONSTRAINT fk_ens_cours_enseignant FOREIGN KEY (enseignant_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    CONSTRAINT fk_ens_cours_cours FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. COURS_PROMOTIONS (Table de jointure)
CREATE TABLE cours_promotions (
    cours_id INT NOT NULL,
    promotion_id INT NOT NULL,
    CONSTRAINT pk_cours_promotions PRIMARY KEY (cours_id, promotion_id),
    CONSTRAINT fk_cours_prom_cours FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE,
    CONSTRAINT fk_cours_prom_promotion FOREIGN KEY (promotion_id) REFERENCES promotions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. ETUDIANTS_PROMOTIONS (Table de jointure)
CREATE TABLE etudiants_promotions (
    etudiant_id INT,
    promotion_id INT,
    CONSTRAINT pk_etudiants_promotions PRIMARY KEY (etudiant_id, promotion_id),
    CONSTRAINT fk_etud_prom_etudiant FOREIGN KEY (etudiant_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    CONSTRAINT fk_etud_prom_promotion FOREIGN KEY (promotion_id) REFERENCES promotions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. FICHIERS
CREATE TABLE fichiers (
    id INT AUTO_INCREMENT,
    nom_origine VARCHAR(255) NOT NULL,
    nom_stockage VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    taille INT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_fichiers PRIMARY KEY (id)
) ENGINE=InnoDB;

-- 8. CONVERSATIONS
CREATE TABLE conversations (
    id INT AUTO_INCREMENT,
    type ENUM('prive', 'public_promotion', 'confidentiel') NOT NULL,
    promotion_id INT NULL,
    expediteur_id INT NULL,
    destinataire_id INT NULL,
    CONSTRAINT pk_conversations PRIMARY KEY (id),
    CONSTRAINT fk_conversations_promotions FOREIGN KEY (promotion_id) REFERENCES promotions(id) ON DELETE SET NULL,
    CONSTRAINT fk_conversations_expediteur FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    CONSTRAINT fk_conversations_destinataire FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 9. MESSAGES
CREATE TABLE messages (
    id INT AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    expediteur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    fichier_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_messages PRIMARY KEY (id),
    CONSTRAINT fk_messages_conversations FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    CONSTRAINT fk_messages_expediteur FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    CONSTRAINT fk_messages_fichiers FOREIGN KEY (fichier_id) REFERENCES fichiers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 10. CONVOCATIONS
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
    CONSTRAINT fk_convocations_expediteur FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. CONVOCATIONS_DESTINATAIRES
CREATE TABLE convocations_destinataires (
    convocation_id INT,
    destinataire_id INT,
    lu TINYINT(1) DEFAULT 0,
    CONSTRAINT pk_convocations_destinataires PRIMARY KEY (convocation_id, destinataire_id),
    CONSTRAINT fk_conv_dest_convocation FOREIGN KEY (convocation_id) REFERENCES convocations(id) ON DELETE CASCADE,
    CONSTRAINT fk_conv_dest_destinataire FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 12. MUR_PEDAGOGIQUE
CREATE TABLE mur_pedagogique (
    id INT AUTO_INCREMENT,
    cours_id INT NOT NULL,
    CONSTRAINT pk_mur_pedagogique PRIMARY KEY (id),
    CONSTRAINT uq_mur_cours UNIQUE (cours_id),
    CONSTRAINT fk_mur_cours FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 13. PUBLICATIONS_MUR
CREATE TABLE publications_mur (
    id INT AUTO_INCREMENT,
    mur_id INT NOT NULL,
    auteur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    fichier_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_publications_mur PRIMARY KEY (id),
    CONSTRAINT fk_pub_mur_base FOREIGN KEY (mur_id) REFERENCES mur_pedagogique(id) ON DELETE CASCADE,
    CONSTRAINT fk_pub_mur_auteur FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    CONSTRAINT fk_pub_mur_fichier FOREIGN KEY (fichier_id) REFERENCES fichiers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 14. VALVE
CREATE TABLE valve (
    id INT AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    CONSTRAINT pk_valve PRIMARY KEY (id)
) ENGINE=InnoDB;

-- 15. ANNONCES_VALVE
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
    CONSTRAINT fk_annonces_valve_base FOREIGN KEY (valve_id) REFERENCES valve(id) ON DELETE CASCADE,
    CONSTRAINT fk_annonces_valve_auteur FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    CONSTRAINT fk_annonces_valve_fichier FOREIGN KEY (fichier_id) REFERENCES fichiers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 16. SESSIONS_UTILISATEUR
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
    CONSTRAINT fk_sessions_utilisateurs FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 17. DEMANDES_ADMINISTRATION
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
) ENGINE=InnoDB;

-- INDEX OPTIMISATION
CREATE INDEX idx_utilisateurs_email ON utilisateurs(email);
CREATE INDEX idx_messages_conversation ON messages(conversation_id);
CREATE INDEX idx_annonces_valve_date ON annonces_valve(created_at DESC);
CREATE INDEX idx_publications_mur_date ON publications_mur(created_at DESC);
