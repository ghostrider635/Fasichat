<?php
namespace App\Entities;

class AdministrateurAcademique extends Utilisateur
{
    public function getDashboardUrl(): string
    {
        return 'dashboard_admin.php';
    }
}
