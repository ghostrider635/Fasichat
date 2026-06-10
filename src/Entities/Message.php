<?php
namespace App\Entities;

class Message
{
    protected int $id;
    protected int $expediteurId;
    protected ?int $destinataireId;
    protected string $contenu;
    protected string $type;
    protected ?string $fichierUrl;
    protected string $dateEnvoi;

    public function __construct(int $id, int $expediteurId, ?int $destinataireId, string $contenu, string $type, ?string $fichierUrl, string $dateEnvoi)
    {
        $this->id = $id;
        $this->expediteurId = $expediteurId;
        $this->destinataireId = $destinataireId;
        $this->contenu = $contenu;
        $this->type = $type;
        $this->fichierUrl = $fichierUrl;
        $this->dateEnvoi = $dateEnvoi;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getExpediteurId(): int
    {
        return $this->expediteurId;
    }

    public function getDestinataireId(): ?int
    {
        return $this->destinataireId;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFichierUrl(): ?string
    {
        return $this->fichierUrl;
    }

    public function getDateEnvoi(): string
    {
        return $this->dateEnvoi;
    }
}
