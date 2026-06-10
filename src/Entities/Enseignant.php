<?php
namespace App\Entities;

class Enseignant extends Utilisateur
{
    public function getDashboardUrl(): string
    {
        return 'dashboard_enseignant.php';
    }
}
