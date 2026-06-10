<?php
require_once 'Utilisateur.php';

/**
 * Classe Enseignant
 * Représente un enseignant dans le système
 */
class Enseignant extends Utilisateur {
    // Propriétés spécifiques aux enseignants
    private $matiere_principale;
    private $bureau;
    private $telephone;
    private $heures_contact;
    private $cours_ids = []; // IDs des cours enseignés
    
    /**
     * Constructeur
     * @param array $donnees Données pour initialiser l'enseignant
     */
    public function __construct($donnees = []) {
        parent::__construct($donnees);
        $this->role = 'enseignant';
    }
    
    // Getters spécifiques
    public function getMatierePrincipale() { return $this->matiere_principale; }
    public function getBureau() { return $this->bureau; }
    public function getTelephone() { return $this->telephone; }
    public function getHeuresContact() { return $this->heures_contact; }
    public function getCoursIds() { return $this->cours_ids; }
    
    // Setters spécifiques
    public function setMatierePrincipale($matiere) { 
        $this->matiere_principale = htmlspecialchars($matiere);
        return $this;
    }
    
    public function setBureau($bureau) { 
        $this->bureau = htmlspecialchars($bureau);
        return $this;
    }
    
    public function setTelephone($telephone) { 
        $this->telephone = htmlspecialchars($telephone);
        return $this;
    }
    
    public function setHeuresContact($heures) { 
        $this->heures_contact = htmlspecialchars($heures);
        return $this;
    }
    
    public function setCoursIds($cours_ids) { 
        $this->cours_ids = is_array($cours_ids) ? $cours_ids : [];
        return $this;
    }
    
    /**
     * Implémentation de la méthode abstraite getPermissions
     * @return array Permissions spécifiques aux enseignants
     */
    public function getPermissions() {
        return [
            // Messagerie
            'envoyer_message_prive' => true,    // Peut envoyer des messages privés à tous
            'envoyer_message_public' => true,   // Peut envoyer des messages publics
            'recevoir_message' => true,         // Peut recevoir tous les types de messages
            'moderer_message_public' => true,   // Peut modérer les messages publics
            
            // Convocations
            'creer_convocation' => false,       // Ne peut pas créer de convocations (réservé au Doyen/Vice-Doyen)
            'participer_convocation' => true,   // Peut participer aux convocations
            'recevoir_convocation' => true,     // Peut recevoir des convocations
            
            // Valve (annonces institutionnelles)
            'publier_valve' => false,           // Ne peut pas publier sur le Valve
            'consulter_valve' => true,          // Peut consulter les annonces du Valve
            
            // Fichiers
            'upload_fichier' => true,           // Peut uploader des fichiers
            'telecharger_fichier' => true,      // Peut télécharger les fichiers
            'partager_fichier_cours' => true,   // Peut partager des fichiers avec les étudiants de ses cours
            
            // Cours (privilèges avancés)
            'creer_cours' => true,              // Peut créer de nouveaux cours
            'modifier_cours' => true,           // Peut modifier ses cours
            'supprimer_cours' => false,         // Ne peut pas supprimer des cours (réservé à l'admin)
            'gerer_etudiants_cours' => true,    // Peut gérer les étudiants de ses cours
            'publier_document_cours' => true,   // Peut publier des documents de cours
            'evaluer_etudiants' => true,        // Peut évaluer les étudiants
            
            // Mur pédagogique
            'publier_mur_pedagogique' => true,  // Peut publier sur le mur pédagogique
            'repondre_questions' => true,       // Peut répondre aux questions des étudiants
            'moderer_mur' => true,              // Peut modérer le mur pédagogique
            
            // Autres
            'voir_profil_etudiant' => true,     // Peut voir les profils des étudiants de ses cours
            'modifier_profil' => true,          // Peut modifier son propre profil
            'voir_statistiques_cours' => true,  // Peut voir les statistiques de ses cours
        ];
    }
    
    /**
     * Créer un nouveau cours
     * @param string $titre Titre du cours
     * @param string $description Description du cours
     * @param array $options Options supplémentaires
     * @return int|false ID du cours créé ou false en cas d'erreur
     */
    public function creerCours($titre, $description, $options = []) {
        if (!$this->aPermission('creer_cours')) {
            return false;
        }
        
        // Logique de création de cours
        // À implémenter avec la base de données
        $cours_id = rand(1, 1000); // Simulé pour l'instant
        $this->cours_ids[] = $cours_id;
        
        return $cours_id;
    }
    
    /**
     * Ajouter un document à un cours
     * @param int $cours_id ID du cours
     * @param string $titre_document Titre du document
     * @param string $chemin_fichier Chemin du fichier
     * @param string $type_document Type de document
     * @return bool Succès de l'opération
     */
    public function ajouterDocumentCours($cours_id, $titre_document, $chemin_fichier, $type_document = 'document') {
        if (!in_array($cours_id, $this->cours_ids)) {
            return false; // L'enseignant n'enseigne pas ce cours
        }
        
        if (!$this->aPermission('publier_document_cours')) {
            return false;
        }
        
        // Logique d'ajout de document
        // À implémenter avec la base de données
        return true;
    }
    
    /**
     * Répondre à une question sur le mur pédagogique
     * @param int $question_id ID de la question
     * @param string $reponse Réponse de l'enseignant
     * @return bool Succès de l'opération
     */
    public function repondreQuestionMur($question_id, $reponse) {
        if (!$this->aPermission('repondre_questions')) {
            return false;
        }
        
        // Logique de réponse
        // À implémenter avec la base de données
        return true;
    }
    
    /**
     * Données spécifiques pour l'affichage
     * @return array
     */
    public function toArray() {
        $data = parent::toArray();
        $data['matiere_principale'] = $this->matiere_principale;
        $data['bureau'] = $this->bureau;
        $data['telephone'] = $this->telephone;
        $data['heures_contact'] = $this->heures_contact;
        $data['cours_count'] = count($this->cours_ids);
        return $data;
    }
}
?>