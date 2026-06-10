<?php
namespace App\Entities;

class Document extends Fichier
{
    public function isDocument(): bool
    {
        return !str_starts_with($this->getTypeMime(), 'image/') && !str_starts_with($this->getTypeMime(), 'video/');
    }
}
