<?php
namespace App\Interfaces;

interface Uploadable
{
    public function getAllowedMimeTypes(): array;
    public function getMaxSize(): int;
}
