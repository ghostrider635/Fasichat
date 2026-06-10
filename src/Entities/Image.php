<?php
namespace App\Entities;

class Image extends Fichier
{
    public function isImage(): bool
    {
        return str_starts_with($this->getTypeMime(), 'image/');
    }
}
