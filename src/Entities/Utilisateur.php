<?php
namespace App\Entities;

abstract class Utilisateur
{
    protected int $id;
    protected string $nom;
    protected string $prenom;
    protected string $email;
    protected string $motDePasse;
    protected string $role;

    public function __construct(int $id, string $nom, string $prenom, string $email, string $motDePasse, string $role)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    abstract public function getDashboardUrl(): string;
}
