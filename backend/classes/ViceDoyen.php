<?php
require_once 'Doyen.php';

/**
 * Classe ViceDoyen
 * Hérite de Doyen et a les mêmes droits de convocation
 * Peut avoir certaines différences mineures dans les permissions
 */
class ViceDoyen extends Doyen {
    
    /**
     * Constructeur
     * @param array $donnees Données pour initialiser le Vice-Doyen
     */
    public function __construct($donnees = []) {
        parent::__construct($donnees);
        $this->role = 'vice_doyen';
    }
    
    /**
     * Surcharge légère des permissions du Doyen
     * Le Vice-Doyen a presque les mêmes droits, sauf certaines actions administratives
     * @return array Permissions spécifiques au Vice-Doyen
     */
    public function getPermissions() {
        // Récupère les permissions de base du Doyen
        $permissions = parent::getPermissions();
        
        // Le Vice-Doyen a TOUS les mêmes droits de convocation que le Doyen
        // Mais peut avoir quelques restrictions administratives
        $permissions['changer_role'] = false;           // Ne peut pas changer le rôle des utilisateurs
        $permissions['configurer_systeme'] = false;     // Ne peut pas configurer le système
        $permissions['supprimer_compte'] = false;       // Ne peut pas supprimer des comptes
        
        // Ajoute des permissions spécifiques au Vice-Doyen
        $permissions['remplacer_doyen'] = true;         // Peut remplacer le Doyen en son absence
        $permissions['gerer_urgence'] = true;           // Peut gérer les situations d'urgence
        
        return $permissions;
    }
    
    // ====================================================
    // MÉTHODES SPÉCIFIQUES AU VICE-DOYEN
    // ====================================================
    
    /**
     * Remplacer le Doyen en son absence
     * Active temporairement des permissions supplémentaires
     * @param bool $activer Si true, active les permissions de remplacement
     * @return bool Succès de l'opération
     */
    public function activerRemplacementDoyen($activer = true) {
        if (!$this->aPermission('remplacer_doyen')) {
            return false;
        }
        
        // Logique d'activation des permissions de remplacement
        // En production, cela pourrait modifier temporairement les permissions
        // ou créer un journal d'audit
        $this->remplacement_actif = $activer;
        
        return true;
    }
    
    /**
     * Gérer une situation d'urgence
     * @param string $type_urgence Type d'urgence
     * @param string $description Description de la situation
     * @param array $actions Actions à entreprendre
     * @return array Rapport de gestion d'urgence
     */
    public function gererUrgence($type_urgence, $description, $actions = []) {
        if (!$this->aPermission('gerer_urgence')) {
            return ['success' => false, 'message' => 'Permission refusée'];
        }
        
        // Types d'urgence supportés
        $urgences_supportees = [
            'technique' => 'Problème technique du système',
            'securite' => 'Problème de sécurité',
            'pedagogique' => 'Urgence pédagogique',
            'administrative' => 'Urgence administrative'
        ];
        
        if (!array_key_exists($type_urgence, $urgences_supportees)) {
            return ['success' => false, 'message' => 'Type d\'urgence non supporté'];
        }
        
        // Logique de gestion d'urgence
        $rapport = [
            'success' => true,
            'type_urgence' => $type_urgence,
            'description' => $description,
            'gestionnaire' => $this->getNomComplet(),
            'date_gestion' => date('Y-m-d H:i:s'),
            'actions_prises' => $actions,
            'statut' => 'en_cours'
        ];
        
        // Actions par défaut selon le type d'urgence
        switch ($type_urgence) {
            case 'technique':
                $rapport['actions_recommandees'] = [
                    'Contacter le support technique',
                    'Notifier les utilisateurs',
                    'Mettre en place une solution temporaire'
                ];
                break;
                
            case 'securite':
                $rapport['actions_recommandees'] = [
                    'Isoler le problème',
                    'Notifier l\'administrateur système',
                    'Auditer les logs de sécurité'
                ];
                break;
                
            case 'pedagogique':
                $rapport['actions_recommandees'] = [
                    'Contacter les enseignants concernés',
                    'Réorganiser les séances si nécessaire',
                    'Communiquer avec les étudiants'
                ];
                break;
        }
        
        return $rapport;
    }
    
    /**
     * Convoquer une réunion d'urgence
     * Version spécifique pour le Vice-Doyen avec priorités d'urgence
     * @param string $sujet Sujet de la réunion
     * @param string $date Date et heure de la réunion
     * @param array $participants IDs des participants
     * @param string $niveau_urgence Niveau d'urgence (faible, moyen, élevé, critique)
     * @param string $lieu Lieu de la réunion
     * @param string $description Description détaillée
     * @return int|false ID de la convocation créée ou false en cas d'erreur
     */
    public function convoquerReunionUrgence($sujet, $date, $participants, $niveau_urgence = 'moyen', $lieu = '', $description = '') {
        if (!$this->aPermission('creer_convocation')) {
            return false;
        }
        
        // Valide le niveau d'urgence
        $niveaux_valides = ['faible', 'moyen', 'élevé', 'critique'];
        if (!in_array($niveau_urgence, $niveaux_valides)) {
            $niveau_urgence = 'moyen';
        }
        
        // Ajoute le préfixe d'urgence au sujet
        $sujet_complet = "[URGENCE: " . strtoupper($niveau_urgence) . "] " . $sujet;
        
        // Utilise la méthode parent pour créer la convocation
        $convocation_id = parent::convoquerReunion($sujet_complet, $date, $participants, $lieu, $description);
        
        if ($convocation_id !== false) {
            // Ajoute des métadonnées d'urgence à la convocation
            // À implémenter avec la base de données
            $this->convocations_urgence[] = [
                'id' => $convocation_id,
                'niveau_urgence' => $niveau_urgence,
                'date_creation' => date('Y-m-d H:i:s')
            ];
        }
        
        return $convocation_id;
    }
    
    /**
     * Données spécifiques pour l'affichage
     * @return array
     */
    public function toArray() {
        $data = parent::toArray();
        $data['role'] = 'vice_doyen';
        $data['role_display'] = 'Vice-Doyen';
        $data['peut_remplacer_doyen'] = $this->aPermission('remplacer_doyen');
        $data['peut_gerer_urgence'] = $this->aPermission('gerer_urgence');
        return $data;
    }
    
    // Propriété pour le suivi des convocations d'urgence
    private $convocations_urgence = [];
    private $remplacement_actif = false;
}
?>