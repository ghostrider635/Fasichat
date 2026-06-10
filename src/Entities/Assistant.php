<?php
namespace App\Entities;

class Assistant extends Utilisateur
{
    public function getDashboardUrl(): string
    {
        return 'dashboard_enseignant.php';
    }
}
