<?php
namespace App\Entities;

class ViceDoyen extends Utilisateur
{
    public function getDashboardUrl(): string
    {
        return 'dashboard_vicedoyen.php';
    }
}
