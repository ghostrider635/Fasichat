<?php
namespace App\Entities;

class Doyen extends Utilisateur
{
    public function getDashboardUrl(): string
    {
        return 'dashboard_admin.php';
    }
}
