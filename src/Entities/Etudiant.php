<?php
namespace App\Entities;

class Etudiant extends Utilisateur
{
    public function getDashboardUrl(): string
    {
        return 'dashboard_etudiant.php';
    }
}
