<?php
namespace App\Entities;

class MurPedagogique
{
    protected int $id;
    protected int $createurId;
    protected string $contenu;
    protected ?string $fichierUrl;
    protected string $datePublication;

    public function __construct(int $id, int $createurId, string $contenu, ?string $fichierUrl, string $datePublication)
    {
        $this->id = $id;
        $this->createurId = $createurId;
        $this->contenu = $contenu;
        $this->fichierUrl = $fichierUrl;
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

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function getFichierUrl(): ?string
    {
        return $this->fichierUrl;
    }

    public function getDatePublication(): string
    {
        return $this->datePublication;
    }
}
