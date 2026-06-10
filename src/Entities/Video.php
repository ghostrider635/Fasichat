<?php
namespace App\Entities;

class Video extends Fichier
{
    public function isVideo(): bool
    {
        return str_starts_with($this->getTypeMime(), 'video/');
    }
}
