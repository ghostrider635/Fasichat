# TODO - Dynamiser Dashboard + Valve + Messagerie média

## Étape 1 — Valve (dynamique)
- [x] Vérifier que `ValveController` + `ValveRepository` existent (`valve_list`, `valve_create`, etc.).
- [x] Mettre à jour `public/assets/js/valve.js` pour charger les annonces depuis `index.php?action=valve_list` et remplacer les cartes statiques.
- [ ] Mettre à jour `public/valve.php` si nécessaire pour correspondre au rendu JS (containers, classes, modals).

## Étape 2 — Messagerie (upload média)
- [x] Vérifier que `FileController` permet déjà l’upload général.
- [ ] Étendre `MessageController::send()` pour accepter un upload (fichier/image/vidéo/voice) et le lier à un message via `messages.fichier_id`.
- [ ] Mettre à jour le front `public/conversation.php` pour envoyer aussi un média (inputs file + preview) et un message texte optionnel.
- [ ] Rendre les messages avec preview (audio player / image / video / lien fichier) dans la liste.

## Étape 3 — Dashboard (dynamique)
- [ ] Remplacer les statistiques/tableaux hardcodés dans `public/dashboard_admin.php` et `public/dashboard_vicedoyen.php` par des données DB.
- [ ] S’assurer que la sidebar/menu gauche est cohérente et navigue correctement.

## Étape 4 — Tests
- [ ] Tester Valve: chargement + publication.
- [ ] Tester conversation: envoi texte + envoi média (image/vidéo/audio/pdf/doc).
- [ ] Tester rôles: Doyen / Vice-Doyen / Apparitaire selon `RoleMiddleware`.

