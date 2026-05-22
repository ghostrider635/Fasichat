-- ============================================
-- DONNÉES DE TEST - FASICHAT CLASSROOM
-- Données de démonstration pour les 6 rôles
-- Version: 1.0
-- Date: 21/05/2026
-- ============================================

-- Désactiver les contraintes de clés étrangères temporairement
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- VIDAGE DES TABLES EXISTANTES
-- ============================================
TRUNCATE TABLE fichiers_messages;
TRUNCATE TABLE participants_convocations;
TRUNCATE TABLE etudiants_cours;
TRUNCATE TABLE annonces_valve;
TRUNCATE TABLE mur_pedagogique;
TRUNCATE TABLE fichiers;
TRUNCATE TABLE messages_prives;
TRUNCATE TABLE messages_publics;
TRUNCATE TABLE convocations;
TRUNCATE TABLE messages;
TRUNCATE TABLE etudiants;
TRUNCATE TABLE enseignants;
TRUNCATE TABLE assistants;
TRUNCATE TABLE doyens;
TRUNCATE TABLE vice_doyens;
TRUNCATE TABLE apparitaires;
TRUNCATE TABLE cours;
TRUNCATE TABLE promotions;
TRUNCATE TABLE utilisateurs;

-- Réactiver les contraintes
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- INSERTION DES DONNÉES
-- ============================================

-- Mot de passe hashé pour tous les utilisateurs : 'password123'
-- (En production, utiliser password_hash() avec bcrypt)

-- 1. INSERTION DES PROMOTIONS
INSERT INTO promotions (nom, annee, niveau) VALUES
('Licence 1 Informatique', 2025, 'L1'),
('Licence 2 Informatique', 2025, 'L2'),
('Licence 3 Informatique', 2025, 'L3'),
('Master 1 Informatique', 2025, 'M1'),
('Master 2 Informatique', 2025, 'M2');

-- 2. INSERTION DES UTILISATEURS (6 rôles)

-- Étudiants
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe_hash, type_utilisateur) VALUES
('Dupont', 'Jean', 'jean.dupont@etudiant.univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('Martin', 'Marie', 'marie.martin@etudiant.univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('Bernard', 'Pierre', 'pierre.bernard@etudiant.univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('Petit', 'Sophie', 'sophie.petit@etudiant.univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('Durand', 'Luc', 'luc.durand@etudiant.univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant');

-- Enseignants
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe_hash, type_utilisateur) VALUES
('Leroy', 'Philippe', 'philippe.leroy@univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'enseignant'),
('Moreau', 'Isabelle', 'isabelle.moreau@univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'enseignant'),
('Simon', 'Thomas', 'thomas.simon@univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'enseignant');

-- Assistant
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe_hash, type_utilisateur) VALUES
('Laurent', 'Nicolas', 'nicolas.laurent@univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assistant');

-- Doyen
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe_hash, type_utilisateur) VALUES
('Michel', 'Robert', 'robert.michel@univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doyen');

-- Vice-Doyen
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe_hash, type_utilisateur) VALUES
('Garcia', 'Claire', 'claire.garcia@univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vice_doyen');

-- Apparitaire
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe_hash, type_utilisateur) VALUES
('Roux', 'David', 'david.roux@univ.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'apparitaire');

-- 3. INSERTION DANS LES TABLES SPÉCIALISÉES

-- Étudiants
INSERT INTO etudiants (id_etudiant, numero_etudiant, promotion_id) VALUES
(1, 'ETU2025001', 1),
(2, 'ETU2025002', 1),
(3, 'ETU2025003', 2),
(4, 'ETU2025004', 2),
(5, 'ETU2025005', 3);

-- Enseignants
INSERT INTO enseignants (id_enseignant, matricule, specialite) VALUES
(6, 'ENS2025001', 'Algorithmique'),
(7, 'ENS2025002', 'Base de données'),
(8, 'ENS2025003', 'Programmation Web');

-- Assistant
INSERT INTO assistants (id_assistant, matricule, enseignant_referent_id) VALUES
(9, 'AST2025001', 6);

-- Doyen
INSERT INTO doyens (id_doyen, matricule) VALUES
(10, 'DOY2025001');

-- Vice-Doyen
INSERT INTO vice_doyens (id_vice_doyen, matricule) VALUES
(11, 'VDY2025001');

-- Apparitaire
INSERT INTO apparitaires (id_apparitaire, matricule) VALUES
(12, 'APP2025001');

-- 4. INSERTION DES COURS
INSERT INTO cours (intitule, code, description, enseignant_id) VALUES
('Algorithmique Avancée', 'ALG301', 'Cours d\'algorithmique pour licence 3', 6),
('Base de Données Relationnelles', 'BDD202', 'Introduction aux bases de données', 7),
('Programmation Web PHP', 'WEB301', 'Développement web avec PHP orienté objet', 8),
('Sécurité Informatique', 'SEC401', 'Cours de sécurité pour master 1', 6),
('Intelligence Artificielle', 'IA501', 'Introduction à l\'IA', 7);

-- 5. INSCRIPTION DES ÉTUDIANTS AUX COURS
INSERT INTO etudiants_cours (etudiant_id, cours_id) VALUES
(1, 1), (1, 2), (1, 3),
(2, 1), (2, 2),
(3, 2), (3, 3),
(4, 3), (4, 4),
(5, 4), (5, 5);

-- 6. INSERTION DES MESSAGES

-- Messages privés (entre étudiants)
INSERT INTO messages (contenu, expediteur_id, type_message) VALUES
('Salut Marie, tu as compris l\'exercice d\'algorithmique ?', 1, 'prive'),
('Oui Jean, c\'est assez simple. On se voit demain pour en parler ?', 2, 'prive'),
('Pierre, tu as le cours de base de données ?', 3, 'prive'),
('Oui, je te l\'envoie tout de suite', 4, 'prive');

INSERT INTO messages_prives (id_message_prive, destinataire_id) VALUES
(1, 2),
(2, 1),
(3, 4),
(4, 3);

-- Messages publics (dans les cours)
INSERT INTO messages (contenu, expediteur_id, type_message) VALUES
('Bonjour à tous, le TP de la semaine prochaine portera sur les arbres binaires', 6, 'public'),
('N\'oubliez pas de rendre votre projet PHP avant vendredi', 8, 'public'),
('La correction de l\'examen sera disponible lundi', 7, 'public');

INSERT INTO messages_publics (id_message_public, cours_id) VALUES
(5, 1),
(6, 3),
(7, 2);

-- Convocations
INSERT INTO messages (contenu, expediteur_id, type_message) VALUES
('Convocation pour la réunion du conseil pédagogique', 10, 'convocation'),
('Réunion d\'urgence sur la réforme des programmes', 11, 'convocation');

INSERT INTO convocations (id_convocation, date_reunion, lieu, objet, duree_estimee, statut) VALUES
(8, '2026-06-15 14:00:00', 'Salle de réunion A101', 'Discussion sur les nouveaux programmes de licence', 120, 'planifiee'),
(9, '2026-06-20 10:00:00', 'Bureau du Doyen', 'Préparation de la rentrée 2026-2027', 90, 'planifiee');

-- Participants aux convocations
INSERT INTO participants_convocations (utilisateur_id, convocation_id, statut_presence) VALUES
(10, 8, 'present'), -- Doyen
(11, 8, 'present'), -- Vice-Doyen
(6, 8, 'en_attente'), -- Enseignant 1
(7, 8, 'en_attente'), -- Enseignant 2
(8, 8, 'absent'), -- Enseignant 3
(10, 9, 'present'), -- Doyen
(11, 9, 'present'), -- Vice-Doyen
(6, 9, 'en_attente'); -- Enseignant 1

-- 7. INSERTION DES ANNONCES VALVE
INSERT INTO annonces_valve (titre, contenu, apparitaire_id, statut, priorite) VALUES
('Fermeture exceptionnelle de la bibliothèque', 'La bibliothèque sera fermée le 25 mai pour travaux. Réouverture le 26 mai à 8h.', 12, 'publiee', 'urgente'),
('Nouveaux horaires de la cafétéria', 'À partir du 1er juin, la cafétéria sera ouverte de 8h à 19h du lundi au vendredi.', 12, 'publiee', 'information'),
('Concours de programmation', 'Inscriptions ouvertes pour le concours annuel de programmation. Date limite : 30 mai.', 12, 'publiee', 'normale'),
('Maintenance des serveurs', 'Maintenance prévue le 28 mai de 2h à 6h. Interruption de service possible.', 12, 'brouillon', 'information');

-- 8. INSERTION DU MUR PÉDAGOGIQUE
INSERT INTO mur_pedagogique (cours_id, contenu, enseignant_id, type_contenu) VALUES
(1, 'Les slides du cours sur les graphes sont disponibles dans la section ressources', 6, 'ressource'),
(2, 'Devoir à rendre pour le 10 juin : conception d\'une base de données pour une bibliothèque', 7, 'devoir'),
(3, 'Rappel : le projet final doit être rendu avant le 15 juin', 8, 'annonce'),
(4, 'Conférence sur la cybersécurité le 25 mai à 14h en amphi B', 6, 'information');

-- 9. INSERTION DES FICHIERS (exemples)
INSERT INTO fichiers (nom_original, nom_serveur, chemin, type_mime, taille, uploader_id) VALUES
('cours_algo.pdf', 'algo_20250521_123456.pdf', '/uploads/documents/algo_20250521_123456.pdf', 'application/pdf', 2048576, 6),
('tp_php.zip', 'tp_php_20250521_654321.zip', '/uploads/documents/tp_php_20250521_654321.zip', 'application/zip', 5123456, 8),
('photo_groupe.jpg', 'groupe_20250521_789012.jpg', '/uploads/images/groupe_20250521_789012.jpg', 'image/jpeg', 1536000, 1),
('presentation_bdd.pptx', 'bdd_20250521_345678.pptx', '/uploads/documents/bdd_20250521_345678.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 8192000, 7);

-- Association fichiers-messages
INSERT INTO fichiers_messages (fichier_id, message_id) VALUES
(1, 5), -- cours_algo.pdf avec message public 5
(2, 6), -- tp_php.zip avec message public 6
(3, 1); -- photo_groupe.jpg avec message privé 1

-- ============================================
-- VÉRIFICATION DES DONNÉES
-- ============================================

-- Comptage des données insérées
SELECT 
    'Utilisateurs' AS table_name, COUNT(*) AS count FROM utilisateurs
UNION ALL
SELECT 'Étudiants', COUNT(*) FROM etudiants
UNION ALL
SELECT 'Enseignants', COUNT(*) FROM enseignants
UNION ALL
SELECT 'Assistants', COUNT(*) FROM assistants
UNION ALL
SELECT 'Doyens', COUNT(*) FROM doyens
UNION ALL
SELECT 'Vice-Doyens', COUNT(*) FROM vice_doyens
UNION ALL
SELECT 'Apparitaires', COUNT(*) FROM apparitaires
UNION ALL
SELECT 'Promotions', COUNT(*) FROM promotions
UNION ALL
SELECT 'Cours', COUNT(*) FROM cours
UNION ALL
SELECT 'Inscriptions cours', COUNT(*) FROM etudiants_cours
UNION ALL
SELECT 'Messages', COUNT(*) FROM messages
UNION ALL
SELECT 'Messages privés', COUNT(*) FROM messages_prives
UNION ALL
SELECT 'Messages publics', COUNT(*) FROM messages_publics
UNION ALL
SELECT 'Convocations', COUNT(*) FROM convocations
UNION ALL
SELECT 'Participants convocations', COUNT(*) FROM participants_convocations
UNION ALL
SELECT 'Annonces Valve', COUNT(*) FROM annonces_valve
UNION ALL
SELECT 'Mur pédagogique', COUNT(*) FROM mur_pedagogique
UNION ALL
SELECT 'Fichiers', COUNT(*) FROM fichiers
UNION ALL
SELECT 'Associations fichiers-messages', COUNT(*) FROM fichiers_messages;

-- ============================================
-- INFORMATIONS DE CONNEXION POUR LES TESTS
-- ============================================

SELECT '=== INFORMATIONS DE CONNEXION POUR LES TESTS ===' AS info;

SELECT 
    u.id_utilisateur,
    u.nom,
    u.prenom,
    u.email,
    u.type_utilisateur,
    'Mot de passe : password123' AS password_info,
    CASE u.type_utilisateur
        WHEN 'etudiant' THEN CONCAT('Numéro étudiant : ', e.numero_etudiant)
        WHEN 'enseignant' THEN CONCAT('Matricule : ', en.matricule)
        WHEN 'assistant' THEN CONCAT('Matricule : ', a.matricule)
        WHEN 'doyen' THEN CONCAT('Matricule : ', d.matricule)
        WHEN 'vice_doyen' THEN CONCAT('Matricule : ', vd.matricule)
        WHEN 'apparitaire' THEN CONCAT('Matricule : ', ap.matricule)
    END AS details
FROM utilisateurs u
LEFT JOIN etudiants e ON u.id_utilisateur = e.id_etudiant
LEFT JOIN enseignants en ON u.id_utilisateur = en.id_enseignant
LEFT JOIN assistants a ON u.id_utilisateur = a.id_assistant
LEFT JOIN doyens d ON u.id_utilisateur = d.id_doyen
LEFT JOIN vice_doyens vd ON u.id_utilisateur = vd.id_vice_doyen
LEFT JOIN apparitaires ap ON u.id_utilisateur = ap.id_apparitaire
ORDER BY u.type_utilisateur, u.nom;

-- ============================================
-- EXEMPLES DE REQUÊTES UTILES POUR LES TESTS
-- ============================================

SELECT '=== EXEMPLES DE REQUÊTES POUR TESTS ===' AS info;

-- 1. Tous les messages d'un étudiant
SELECT 'Messages de l\'étudiant Jean Dupont (ID:1)' AS requete;
SELECT 
    m.id_message,
    m.contenu,
    m.date_envoi,
    m.type_message,
    CASE 
        WHEN m.type_message = 'prive' THEN CONCAT('À: ', u_dest.nom, ' ', u_dest.prenom)
        WHEN m.type_message = 'public' THEN CONCAT('Cours: ', c.intitule)
        WHEN m.type_message = 'convocation' THEN 'Convocation'
    END AS destinataire
FROM messages m
LEFT JOIN messages_prives mp ON m.id_message = mp.id_message_prive AND m.type_message = 'prive'
LEFT JOIN utilisateurs u_dest ON mp.destinataire_id = u_dest.id_utilisateur
LEFT JOIN messages_publics mpub ON m.id_message = mpub.id_message_public AND m.type_message = 'public'
LEFT JOIN cours c ON mpub.cours_id = c.id_cours
WHERE m.expediteur_id = 1
ORDER BY m.date_envoi DESC;

-- 2. Tous les cours d'un enseignant avec étudiants inscrits
SELECT 'Cours de l\'enseignant Philippe Leroy (ID:6) avec étudiants' AS requete;
SELECT 
    c.intitule,
    c.code,
    COUNT(ec.etudiant_id) AS nombre_etudiants,
    GROUP_CONCAT(CONCAT(e.nom, ' ', e.prenom) SEPARATOR ', ') AS etudiants
FROM cours c
LEFT JOIN etudiants_cours ec ON c.id_cours = ec.cours_id
LEFT JOIN etudiants et ON ec.etudiant_id = et.id_etudiant
LEFT JOIN utilisateurs e ON et.id_etudiant = e.id_utilisateur
WHERE c.enseignant_id = 6
GROUP BY c.id_cours, c.intitule, c.code;

-- 3. Annonces du valve publiées
SELECT 'Annonces du valve publiées' AS requete;
SELECT 
    av.titre,
    av.contenu,
    av.date_publication,
    av.priorite,
    CONCAT(u.nom, ' ', u.prenom) AS publie_par
FROM annonces_valve av
JOIN apparitaires ap ON av.apparitaire_id = ap.id_apparitaire
JOIN utilisateurs u ON ap.id_apparitaire = u.id_utilisateur
WHERE av.statut = 'publiee'
ORDER BY av.date_publication DESC;

-- 4. Convocations avec participants
SELECT 'Convocations avec détails des participants' AS requete;
SELECT 
    c.id_convocation,
    m.contenu AS objet,
    c.date_reunion,
    c.lieu,
    c.statut,
    COUNT(pc.utilisateur_id) AS total_participants,
    SUM(CASE WHEN pc.statut_presence = 'present' THEN 1 ELSE 0 END) AS confirmes_presents,
    SUM(CASE WHEN pc.statut_presence = 'absent' THEN 1 ELSE 0 END) AS confirmes_absents
FROM convocations c
JOIN messages m ON c.id_convocation = m.id_message
LEFT JOIN participants_convocations pc ON c.id_convocation = pc.convocation_id
GROUP BY c.id_convocation, m.contenu, c.date_reunion, c.lieu, c.statut;

-- ============================================
-- FIN DU SCRIPT
-- ============================================

SELECT 'Données de test insérées avec succès!' AS message;