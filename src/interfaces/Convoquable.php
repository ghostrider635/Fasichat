<?php
namespace App\Interfaces;

interface Convoquable {
    public function convoquer(string $objet, string $message, string $lieu, string $date, string $heure): bool;
}