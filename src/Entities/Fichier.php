<?php
namespace App\Entities;

class Fichier
{
    protected int $id;
    protected int $utilisateurId;
    protected string $nomOriginal;
    protected string $cheminStockage;
    protected string $typeMime;
    protected int $taille;
    protected string $dateAjout;

    public function __construct(int $id, int $utilisateurId, string $nomOriginal, string $cheminStockage, string $typeMime, int $taille, string $dateAjout)
    {
        $this->id = $id;
        $this->utilisateurId = $utilisateurId;
        $this->nomOriginal = $nomOriginal;
        $this->cheminStockage = $cheminStockage;
        $this->typeMime = $typeMime;
        $this->taille = $taille;
        $this->dateAjout = $dateAjout;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUtilisateurId(): int
    {
        return $this->utilisateurId;
    }

    public function getNomOriginal(): string
    {
        return $this->nomOriginal;
    }

    public function getCheminStockage(): string
    {
        return $this->cheminStockage;
    }

    public function getTypeMime(): string
    {
        return $this->typeMime;
    }

    public function getTaille(): int
    {
        return $this->taille;
    }

    public function getDateAjout(): string
    {
        return $this->dateAjout;
    }
}
