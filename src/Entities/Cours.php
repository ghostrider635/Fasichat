<?php
namespace App\Entities;

class Cours
{
    protected int $id;
    protected string $nom;
    protected string $description;
    protected int $enseignantId;

    public function __construct(int $id, string $nom, string $description, int $enseignantId)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->enseignantId = $enseignantId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEnseignantId(): int
    {
        return $this->enseignantId;
    }
}
