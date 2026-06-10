<?php
/**
 * Classe abstraite Utilisateur
 * Base de tous les types d'utilisateurs du système
 */
abstract class Utilisateur {
    // Propriétés communes à tous les utilisateurs
    protected $id;
    protected $nom;
    protected $prenom;
    protected $email;
    protected $role;
    protected $date_inscription;
    protected $actif = true;
    
    /**
     * Constructeur
     * @param array $donnees Données pour initialiser l'utilisateur
     */
    public function __construct($donnees = []) {
        if (!empty($donnees)) {
            $this->hydrate($donnees);
        }
    }
    
    /**
     * Hydrate l'objet avec les données
     * @param array $donnees Données à assigner
     */
    protected function hydrate($donnees) {
        foreach ($donnees as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function getDateInscription() { return $this->date_inscription; }
    public function getActif() { return $this->actif; }
    
    /**
     * Retourne le nom complet
     * @return string
     */
    public function getNomComplet() {
        return $this->prenom . ' ' . $this->nom;
    }
    
    /**
     * Retourne l'initiale du prénom
     * @return string
     */
    public function getInitiale() {
        return strtoupper(substr($this->prenom, 0, 1));
    }
    
    // Setters
    public function setId($id) { 
        $this->id = (int) $id;
        return $this;
    }
    
    public function setNom($nom) { 
        $this->nom = htmlspecialchars($nom);
        return $this;
    }
    
    public function setPrenom($prenom) { 
        $this->prenom = htmlspecialchars($prenom);
        return $this;
    }
    
    public function setEmail($email) { 
        $this->email = htmlspecialchars($email);
        return $this;
    }
    
    public function setRole($role) { 
        $this->role = htmlspecialchars($role);
        return $this;
    }
    
    public function setDateInscription($date) { 
        $this->date_inscription = $date;
        return $this;
    }
    
    public function setActif($actif) { 
        $this->actif = (bool) $actif;
        return $this;
    }
    
    // Méthodes communes
    public function peutEnvoyerMessage() { return true; }
    public function peutRecevoirMessage() { return true; }
    public function peutUploadFichier() { return true; }
    
    /**
     * Méthode abstraite : doit être implémentée par chaque type d'utilisateur
     * @return array Permissions spécifiques au rôle
     */
    abstract public function getPermissions();
    
    /**
     * Vérifie si l'utilisateur a une permission spécifique
     * @param string $permission Permission à vérifier
     * @return bool
     */
    public function aPermission($permission) {
        $permissions = $this->getPermissions();
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }
    
    /**
     * Méthode pour formater les données pour l'affichage
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'role' => $this->role,
            'nom_complet' => $this->getNomComplet(),
            'initiale' => $this->getInitiale(),
            'actif' => $this->actif,
            'date_inscription' => $this->date_inscription
        ];
    }
}
?>