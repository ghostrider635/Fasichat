<?php
/**
 * Interface Convocable
 * Définit les méthodes pour les utilisateurs pouvant créer des convocations
 */
interface Convocable {
    /**
     * Convoquer une réunion
     * @param string $sujet Sujet de la réunion
     * @param string $date Date et heure de la réunion
     * @param array $participants IDs des participants
     * @param string $lieu Lieu de la réunion
     * @param string $description Description détaillée
     * @return int|false ID de la convocation créée ou false en cas d'erreur
     */
    public function convoquerReunion($sujet, $date, $participants, $lieu = '', $description = '');
    
    /**
     * Annuler une convocation
     * @param int $id_convocation ID de la convocation à annuler
     * @return bool Succès de l'opération
     */
    public function annulerConvocation($id_convocation);
    
    /**
     * Lister les convocations créées par cet utilisateur
     * @param string|null $statut Filtrer par statut (pending, confirmed, cancelled, completed)
     * @return array Liste des convocations
     */
    public function listerConvocations($statut = null);
    
    /**
     * Valider une convocation (changer son statut)
     * @param int $id_convocation ID de la convocation
     * @param string $nouveau_statut Nouveau statut
     * @return bool Succès de l'opération
     */
    public function validerConvocation($id_convocation, $nouveau_statut);
    
    /**
     * Ajouter des participants à une convocation existante
     * @param int $id_convocation ID de la convocation
     * @param array $nouveaux_participants IDs des nouveaux participants
     * @return bool Succès de l'opération
     */
    public function ajouterParticipants($id_convocation, $nouveaux_participants);
    
    /**
     * Récupérer une convocation spécifique
     * @param int $id_convocation ID de la convocation
     * @return array|null Données de la convocation ou null si non trouvée
     */
    public function getConvocation($id_convocation);
}
?>