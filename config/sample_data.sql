USE fasichat_classroom;

-- Rôles de base
INSERT INTO roles (nom) VALUES
('Doyen'),
('Vice-Doyen'),
('Administrateur Academique'),
('Apparitaire'),
('Enseignant'),
('Etudiant');

-- Promotions
INSERT INTO promotions (nom) VALUES
('L1 Informatique'),
('L2 Informatique'),
('L3 Informatique');

-- Cours
INSERT INTO cours (nom, code) VALUES
('Programmation Web', 'WEB101'),
('Base de Donnees', 'BDD202'),
('Algorithmique', 'ALG303');

-- Utilisateurs de démonstration
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role_id) VALUES
('Diop', 'Mamadou', 'doyen@fasi.edu', '$2y$12$285OWHV330FShKQ5M6OMSOKTKEGjqgAKGEUgQOe5UORx25qb0kAEK', 1),
('Ndiaye', 'Aminata', 'vicedoyen@fasi.edu', '$2y$12$RHzBOlm5LFU7ZOG6O4XhA.o/ECUNW85qi8sxw28BP/M2KHU0vTuC6', 2),
('Fall', 'Awa', 'admin@fasi.edu', '$2y$10$wRLxGFS9elTSEG9xhZWdPOq76p67PVpSKtqatmWmr7dzvZXOh0PZC', 3),
('Sow', 'Ibrahima', 'apparitaire@fasi.edu', '$2y$10$V7.k/aXrFKMJq8s09i0QMOlVlIVTCsuW4Sm1TrZFY3bmbAvuFjuPy', 4),
('Coulibaly', 'Seynabou', 'enseignant@fasi.edu', '$2y$10$w4tpSqlFUZNnOJwd24SKZul8fb9jVDwvLxwLSQWnMm0y20D3VGP/i', 5),
('Sarr', 'Fatou', 'etudiant@fasi.edu', '$2y$12$MQ8Q4TQrt0yrN5FDxBHrq.1nsfjXvM9fE1HRBgUck1OuO2choZe.q', 6);

-- Liaisons enseignant / cours
INSERT INTO enseignants_cours (enseignant_id, cours_id) VALUES
(5, 1),
(5, 2);

-- Liaisons cours / promotions
INSERT INTO cours_promotions (cours_id, promotion_id) VALUES
(1, 2),
(2, 2),
(3, 1);

-- Liaisons étudiant / promotions
INSERT INTO etudiants_promotions (etudiant_id, promotion_id) VALUES
(6, 2);

-- Valve de démonstration
INSERT INTO valve (nom) VALUES
('Panneau FasiChat');

-- Exemple d'annonces Valve
INSERT INTO annonces_valve (valve_id, auteur_id, titre, contenu) VALUES
(1, 5, 'Bienvenue sur FasiChat', 'La plateforme est opérationnelle pour les enseignants et les étudiants.');

-- Exemple de convocation
INSERT INTO convocations (expediteur_id, objet, date_convocation, heure_convocation, lieu, message) VALUES
(1, 'Réunion de coordination', '2025-04-10', '10:00:00', 'Salle B101', 'Veuillez assister à la réunion de coordination pédagogique.');
INSERT INTO convocations_destinataires (convocation_id, destinataire_id) VALUES
(1, 6);

-- Exemple d'utilisation :
-- Email : doyen@fasi.edu, mot de passe : Doyen123!
-- Email : vicedoyen@fasi.edu, mot de passe : ViceDoyen123!
-- Email : admin@fasi.edu, mot de passe : Admin123!
-- Email : apparitaire@fasi.edu, mot de passe : Apparitaire123!
-- Email : enseignant@fasi.edu, mot de passe : Enseignant123!
-- Email : etudiant@fasi.edu, mot de passe : Etudiant123!
