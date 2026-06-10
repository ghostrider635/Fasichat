<?php
namespace App\Entities;

class MessagePrive extends Message
{
    public function __construct(int $id, int $expediteurId, int $destinataireId, string $contenu, ?string $fichierUrl, string $dateEnvoi)
    {
        parent::__construct($id, $expediteurId, $destinataireId, $contenu, 'private', $fichierUrl, $dateEnvoi);
    }
}
