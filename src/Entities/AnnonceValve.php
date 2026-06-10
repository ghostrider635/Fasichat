<?php
namespace App\Entities;

class AnnonceValve
{
    protected int $id;
    protected int $createurId;
    protected string $titre;
    protected string $contenu;
    protected string $datePublication;

    public function __construct(int $id, int $createurId, string $titre, string $contenu, string $datePublication)
    {
        $this->id = $id;
        $this->createurId = $createurId;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->datePublication = $datePublication;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreateurId(): int
    {
        return $this->createurId;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function getDatePublication(): string
    {
        return $this->datePublication;
    }
}
