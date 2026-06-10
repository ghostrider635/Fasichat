<?php
namespace App\Entities;

class Convocation
{
    protected int $id;
    protected string $titre;
    protected string $description;
    protected string $dateDebut;
    protected string $dateFin;
    protected int $createurId;
    protected ?string $fichierPath;

    public function __construct(int $id, string $titre, string $description, string $dateDebut, string $dateFin, int $createurId, ?string $fichierPath = null)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->createurId = $createurId;
        $this->fichierPath = $fichierPath;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDateDebut(): string
    {
        return $this->dateDebut;
    }

    public function getDateFin(): string
    {
        return $this->dateFin;
    }

    public function getCreateurId(): int
    {
        return $this->createurId;
    }

    public function getFichierPath(): ?string
    {
        return $this->fichierPath;
    }
}
