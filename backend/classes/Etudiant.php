<?php
require_once 'Utilisateur.php';

/**
 * Classe Etudiant
 * Représente un étudiant dans le système
 */
class Etudiant extends Utilisateur {
    // Propriétés spécifiques aux étudiants
    private $numero_etudiant;
    private $promotion_id;
    private $annee_etude;
    private $groupe_tp;
    
    /**
     * Constructeur
     * @param array $donnees Données pour initialiser l'étudiant
     */
    public function __construct($donnees = []) {
        parent::__construct($donnees);
        $this->role = 'etudiant';
    }
    
    // Getters spécifiques
    public function getNumeroEtudiant() { return $this->numero_etudiant; }
    public function getPromotionId() { return $this->promotion_id; }
    public function getAnneeEtude() { return $this->annee_etude; }
    public function getGroupeTp() { return $this->groupe_tp; }
    
    // Setters spécifiques
    public function setNumeroEtudiant($numero) { 
        $this->numero_etudiant = htmlspecialchars($numero);
        return $this;
    }
    
    public function setPromotionId($promotion_id) { 
        $this->promotion_id = (int) $promotion_id;
        return $this;
    }
    
    public function setAnneeEtude($annee) { 
        $this->annee_etude = (int) $annee;
        return $this;
    }
    
    public function setGroupeTp($groupe) { 
        $this->groupe_tp = htmlspecialchars($groupe);
        return $this;
    }
    
    /**
     * Implémentation de la méthode abstraite getPermissions
     * @return array Permissions spécifiques aux étudiants
     */
    public function getPermissions() {
        return [
            // Messagerie
            'envoyer_message_prive' => true,    // Peut envoyer des messages privés à d'autres étudiants
            'envoyer_message_public' => false,  // Ne peut pas envoyer de messages publics
            'recevoir_message' => true,         // Peut recevoir tous les types de messages
            'repondre_message_public' => true,  // Peut répondre aux messages publics
            
            // Convocations
            'creer_convocation' => false,       // Ne peut pas créer de convocations
            'participer_convocation' => true,   // Peut participer aux convocations
            'annuler_participation' => true,    // Peut annuler sa participation
            
            // Valve (annonces institutionnelles)
            'publier_valve' => false,           // Ne peut pas publier sur le Valve
            'consulter_valve' => true,          // Peut consulter les annonces du Valve
            
            // Fichiers
            'upload_fichier' => true,           // Peut uploader des fichiers
            'telecharger_fichier' => true,      // Peut télécharger les fichiers partagés
            'partager_fichier' => true,         // Peut partager des fichiers avec d'autres étudiants
            
            // Cours
            'consulter_cours' => true,          // Peut consulter les cours
            'poser_question' => true,           // Peut poser des questions sur le mur pédagogique
            'telecharger_document_cours' => true, // Peut télécharger les documents de cours
            
            // Autres
            'voir_profil_autre' => true,        // Peut voir les profils des autres étudiants
            'modifier_profil' => true,          // Peut modifier son propre profil
            'voir_statistiques' => false,       // Ne peut pas voir les statistiques avancées
        ];
    }
    
    /**
     * Récupérer les cours auxquels l'étudiant est inscrit
     * @return array Liste des cours
     */
    public function getCours() {
        // À implémenter avec la base de données
        return [];
    }
    
    /**
     * Vérifie si l'étudiant peut envoyer un message à un autre utilisateur
     * @param Utilisateur $destinataire Destinataire potentiel
     * @return bool
     */
    public function peutEnvoyerMessageA($destinataire) {
        // Un étudiant peut envoyer des messages à :
        // 1. D'autres étudiants (privé)
        // 2. Enseignants/Assistant (public seulement)
        if ($destinataire instanceof Etudiant) {
            return true; // Message privé entre étudiants
        }
        return false; // Pour les autres types, doit passer par message public
    }
    
    /**
     * Données spécifiques pour l'affichage
     * @return array
     */
    public function toArray() {
        $data = parent::toArray();
        $data['numero_etudiant'] = $this->numero_etudiant;
        $data['promotion_id'] = $this->promotion_id;
        $data['annee_etude'] = $this->annee_etude;
        $data['groupe_tp'] = $this->groupe_tp;
        return $data;
    }
}
?>