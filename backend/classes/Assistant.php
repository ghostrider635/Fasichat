<?php
require_once 'Enseignant.php';

/**
 * Classe Assistant
 * Hérite de Enseignant mais avec certaines restrictions
 * A les mêmes privilèges de base que l'enseignant sauf certaines actions
 */
class Assistant extends Enseignant {
    
    /**
     * Constructeur
     * @param array $donnees Données pour initialiser l'assistant
     */
    public function __construct($donnees = []) {
        parent::__construct($donnees);
        $this->role = 'assistant';
    }
    
    /**
     * Surcharge des permissions de l'enseignant
     * @return array Permissions spécifiques aux assistants
     */
    public function getPermissions() {
        // Récupère les permissions de base de l'enseignant
        $permissions = parent::getPermissions();
        
        // Modifie certaines permissions spécifiques aux assistants
        $permissions['creer_cours'] = false;            // Assistant ne peut pas créer de nouveaux cours
        $permissions['modifier_cours'] = false;         // Assistant ne peut pas modifier les cours
        $permissions['supprimer_cours'] = false;        // Assistant ne peut pas supprimer des cours
        $permissions['gerer_etudiants_cours'] = false;  // Assistant ne peut pas gérer les étudiants
        $permissions['evaluer_etudiants'] = false;      // Assistant ne peut pas évaluer les étudiants
        
        // Permissions supplémentaires spécifiques aux assistants
        $permissions['assister_cours'] = true;          // Peut assister aux cours
        $permissions['preparer_tp'] = true;             // Peut préparer les travaux pratiques
        $permissions['corriger_tp'] = true;             // Peut corriger les TP
        $permissions['gerer_travaux'] = true;           // Peut gérer les travaux à rendre
        
        return $permissions;
    }
    
    /**
     * Préparer un travail pratique pour un cours
     * @param int $cours_id ID du cours
     * @param string $titre_tp Titre du TP
     * @param string $description Description du TP
     * @param string $date_limite Date limite de rendu
     * @return int|false ID du TP créé ou false en cas d'erreur
     */
    public function preparerTP($cours_id, $titre_tp, $description, $date_limite) {
        if (!$this->aPermission('preparer_tp')) {
            return false;
        }
        
        // Vérifie que l'assistant est assigné à ce cours
        if (!in_array($cours_id, $this->getCoursIds())) {
            return false;
        }
        
        // Logique de création de TP
        // À implémenter avec la base de données
        $tp_id = rand(1, 1000); // Simulé pour l'instant
        return $tp_id;
    }
    
    /**
     * Corriger un travail pratique
     * @param int $tp_id ID du TP
     * @param int $etudiant_id ID de l'étudiant
     * @param float $note Note attribuée
     * @param string $commentaires Commentaires sur la correction
     * @return bool Succès de l'opération
     */
    public function corrigerTP($tp_id, $etudiant_id, $note, $commentaires = '') {
        if (!$this->aPermission('corriger_tp')) {
            return false;
        }
        
        // Logique de correction de TP
        // À implémenter avec la base de données
        return true;
    }
    
    /**
     * Gérer les travaux à rendre pour un cours
     * @param int $cours_id ID du cours
     * @return array Liste des travaux et leurs statuts
     */
    public function gererTravauxCours($cours_id) {
        if (!$this->aPermission('gerer_travaux')) {
            return [];
        }
        
        // Vérifie que l'assistant est assigné à ce cours
        if (!in_array($cours_id, $this->getCoursIds())) {
            return [];
        }
        
        // Logique de gestion des travaux
        // À implémenter avec la base de données
        return [
            'travaux_en_cours' => [],
            'travaux_corriges' => [],
            'travaux_en_retard' => []
        ];
    }
    
    /**
     * Assister un enseignant pour un cours
     * @param int $cours_id ID du cours
     * @param int $enseignant_id ID de l'enseignant principal
     * @return bool Succès de l'assignation
     */
    public function assisterCours($cours_id, $enseignant_id) {
        if (!$this->aPermission('assister_cours')) {
            return false;
        }
        
        // Ajoute le cours à la liste des cours de l'assistant
        $cours_ids = $this->getCoursIds();
        if (!in_array($cours_id, $cours_ids)) {
            $cours_ids[] = $cours_id;
            $this->setCoursIds($cours_ids);
        }
        
        // Logique d'assignation d'assistant à un cours
        // À implémenter avec la base de données
        return true;
    }
    
    /**
     * Données spécifiques pour l'affichage
     * @return array
     */
    public function toArray() {
        $data = parent::toArray();
        $data['role'] = 'assistant'; // S'assure que le rôle est bien "assistant"
        return $data;
    }
}
?>