<?php
namespace App\Entities;

class MessagePublic extends Message
{
    public function __construct(int $id, int $expediteurId, string $contenu, ?string $fichierUrl, string $dateEnvoi)
    {
        parent::__construct($id, $expediteurId, null, $contenu, 'public', $fichierUrl, $dateEnvoi);
    }
}
