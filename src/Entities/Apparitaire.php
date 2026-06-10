<?php
namespace App\Entities;

class Apparitaire extends Utilisateur
{
    public function getDashboardUrl(): string
    {
        return 'dashboard_apparitaire.php';
    }
}
