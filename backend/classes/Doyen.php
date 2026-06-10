<?php
require_once 'Utilisateur.php';
require_once 'Convocable.php';

/**
 * Classe Doyen
 * Représente le Doyen de la faculté
 * Implémente l'interface Convocable
 */
class Doyen extends Utilisateur implements Convocable {
    // Propriétés spécifiques au Doyen
    private $faculte;
    private $telephone_bureau;
    private $secretariat;
    private $convocations_crees = []; // IDs des convocations créées
    
    /**
     * Constructeur
     * @param array $donnees Données pour initialiser le Doyen
     */
    public function __construct($donnees = []) {
        parent::__construct($donnees);
        $this->role = 'doyen';
    }
    
    // Getters spécifiques
    public function getFaculte() { return $this->faculte; }
    public function getTelephoneBureau() { return $this->telephone_bureau; }
    public function getSecretariat() { return $this->secretariat; }
    public function getConvocationsCrees() { return $this->convocations_crees; }
    
    // Setters spécifiques
    public function setFaculte($faculte) { 
        $this->faculte = htmlspecialchars($faculte);
        return $this;
    }
    
    public function setTelephoneBureau($telephone) { 
        $this->telephone_bureau = htmlspecialchars($telephone);
        return $this;
    }
    
    public function setSecretariat($secretariat) { 
        $this->secretariat = htmlspecialchars($secretariat);
        return $this;
    }
    
    public function setConvocationsCrees($convocations) { 
        $this->convocations_crees = is_array($convocations) ? $convocations : [];
        return $this;
    }
    
    /**
     * Implémentation de la méthode abstraite getPermissions
     * @return array Permissions spécifiques au Doyen
     */
    public function getPermissions() {
        return [
            // Messagerie
            'envoyer_message_prive' => true,    // Peut envoyer des messages privés à tous
            'envoyer_message_public' => true,   // Peut envoyer des messages publics
            'recevoir_message' => true,         // Peut recevoir tous les types de messages
            'moderer_tout_message' => true,     // Peut modérer tous les messages
            
            // Convocations (PRIVILÈGE EXCLUSIF)
            'creer_convocation' => true,        // Peut créer des convocations
            'annuler_convocation' => true,      // Peut annuler n'importe quelle convocation
            'modifier_convocation' => true,     // Peut modifier toutes les convocations
            'inviter_tout_monde' => true,       // Peut inviter n'importe qui aux convocations
            
            // Valve (annonces institutionnelles)
            'publier_valve' => false,           // Ne peut pas publier sur le Valve (réservé à l'apparitaire)
            'consulter_valve' => true,          // Peut consulter les annonces du Valve
            'moderer_valve' => true,            // Peut modérer le contenu du Valve
            
            // Fichiers
            'upload_fichier' => true,           // Peut uploader des fichiers
            'telecharger_fichier' => true,      // Peut télécharger tous les fichiers
            'supprimer_fichier' => true,        // Peut supprimer n'importe quel fichier
            'voir_tous_fichiers' => true,       // Peut voir tous les fichiers du système
            
            // Cours
            'voir_tous_cours' => true,          // Peut voir tous les cours
            'modifier_tout_cours' => true,      // Peut modifier n'importe quel cours
            'assigner_enseignants' => true,     // Peut assigner des enseignants aux cours
            'gerer_promotions' => true,         // Peut gérer les promotions
            
            // Utilisateurs
            'voir_tous_profils' => true,        // Peut voir tous les profils
            'modifier_tout_profil' => true,     // Peut modifier n'importe quel profil
            'activer_desactiver' => true,       // Peut activer/désactiver les comptes
            'changer_role' => true,             // Peut changer le rôle des utilisateurs
            
            // Statistiques et rapports
            'voir_statistiques_completes' => true, // Accès aux statistiques complètes
            'generer_rapports' => true,         // Peut générer des rapports
            'exporter_donnees' => true,         // Peut exporter les données
            
            // Administration système
            'configurer_systeme' => true,       // Peut configurer le système
            'gerer_parametres' => true,         // Peut gérer les paramètres globaux
        ];
    }
    
    // ====================================================
    // IMPLÉMENTATION DE L'INTERFACE CONVOCABLE
    // ====================================================
    
    /**
     * Convoquer une réunion
     * @param string $sujet Sujet de la réunion
     * @param string $date Date et heure de la réunion
     * @param array $participants IDs des participants
     * @param string $lieu Lieu de la réunion
     * @param string $description Description détaillée
     * @return int|false ID de la convocation créée ou false en cas d'erreur
     */
    public function convoquerReunion($sujet, $date, $participants, $lieu = '', $description = '') {
        if (!$this->aPermission('creer_convocation')) {
            return false;
        }
        
        // Validation des paramètres
        if (empty($sujet) || empty($date) || empty($participants)) {
            return false;
        }
        
        // Logique de création de convocation
        // À implémenter avec la base de données
        $convocation_id = rand(1000, 9999); // Simulé pour l'instant
        
        // Ajoute à la liste des convocations créées
        $this->convocations_crees[] = $convocation_id;
        
        return $convocation_id;
    }
    
    /**
     * Annuler une convocation
     * @param int $id_convocation ID de la convocation à annuler
     * @return bool Succès de l'opération
     */
    public function annulerConvocation($id_convocation) {
        if (!$this->aPermission('annuler_convocation')) {
            return false;
        }
        
        // Logique d'annulation de convocation
        // À implémenter avec la base de données
        $index = array_search($id_convocation, $this->convocations_crees);
        if ($index !== false) {
            unset($this->convocations_crees[$index]);
        }
        
        return true;
    }
    
    /**
     * Lister les convocations créées par cet utilisateur
     * @param string|null $statut Filtrer par statut (pending, confirmed, cancelled, completed)
     * @return array Liste des convocations
     */
    public function listerConvocations($statut = null) {
        // Logique de listing des convocations
        // À implémenter avec la base de données
        $convocations = [];
        
        // Simulation de données
        foreach ($this->convocations_crees as $id) {
            $convocations[] = [
                'id' => $id,
                'sujet' => 'Réunion ' . $id,
                'date' => date('Y-m-d H:i:s', strtotime('+' . $id . ' days')),
                'statut' => 'pending',
                'participants_count' => rand(3, 10)
            ];
        }
        
        // Filtrage par statut si demandé
        if ($statut !== null) {
            $convocations = array_filter($convocations, function($conv) use ($statut) {
                return $conv['statut'] === $statut;
            });
        }
        
        return array_values($convocations);
    }
    
    /**
     * Valider une convocation (changer son statut)
     * @param int $id_convocation ID de la convocation
     * @param string $nouveau_statut Nouveau statut
     * @return bool Succès de l'opération
     */
    public function validerConvocation($id_convocation, $nouveau_statut) {
        // Vérifie que le Doyen a créé cette convocation
        if (!in_array($id_convocation, $this->convocations_crees)) {
            // Le Doyen peut valider n'importe quelle convocation
            // Cette vérification est optionnelle pour le Doyen
        }
        
        // Logique de validation
        // À implémenter avec la base de données
        return true;
    }
    
    /**
     * Ajouter des participants à une convocation existante
     * @param int $id_convocation ID de la convocation
     * @param array $nouveaux_participants IDs des nouveaux participants
     * @return bool Succès de l'opération
     */
    public function ajouterParticipants($id_convocation, $nouveaux_participants) {
        if (!$this->aPermission('inviter_tout_monde')) {
            return false;
        }
        
        // Logique d'ajout de participants
        // À implémenter avec la base de données
        return true;
    }
    
    /**
     * Récupérer une convocation spécifique
     * @param int $id_convocation ID de la convocation
     * @return array|null Données de la convocation ou null si non trouvée
     */
    public function getConvocation($id_convocation) {
        // Logique de récupération d'une convocation
        // À implémenter avec la base de données
        foreach ($this->listerConvocations() as $convocation) {
            if ($convocation['id'] == $id_convocation) {
                return $convocation;
            }
        }
        
        return null;
    }
    
    // ====================================================
    // MÉTHODES SPÉCIFIQUES AU DOYEN
    // ====================================================
    
    /**
     * Assigner un enseignant à un cours
     * @param int $enseignant_id ID de l'enseignant
     * @param int $cours_id ID du cours
     * @return bool Succès de l'opération
     */
    public function assignerEnseignantCours($enseignant_id, $cours_id) {
        if (!$this->aPermission('assigner_enseignants')) {
            return false;
        }
        
        // Logique d'assignation
        // À implémenter avec la base de données
        return true;
    }
    
    /**
     * Générer un rapport d'activité
     * @param string $periode Période du rapport (mensuel, trimestriel, annuel)
     * @return array Données du rapport
     */
    public function genererRapportActivite($periode = 'mensuel') {
        if (!$this->aPermission('generer_rapports')) {
            return [];
        }
        
        // Logique de génération de rapport
        // À implémenter avec la base de données
        return [
            'periode' => $periode,
            'date_generation' => date('Y-m-d H:i:s'),
            'statistiques' => [
                'utilisateurs_actifs' => rand(50, 200),
                'messages_envoyes' => rand(100, 1000),
                'convocations_crees' => count($this->convocations_crees),
                'fichiers_uploades' => rand(20, 100)
            ]
        ];
    }
    
    /**
     * Données spécifiques pour l'affichage
     * @return array
     */
    public function toArray() {
        $data = parent::toArray();
        $data['faculte'] = $this->faculte;
        $data['telephone_bureau'] = $this->telephone_bureau;
        $data['secretariat'] = $this->secretariat;
        $data['convocations_count'] = count($this->convocations_crees);
        $data['role_display'] = 'Doyen de la faculté';
        return $data;
    }
}
?>