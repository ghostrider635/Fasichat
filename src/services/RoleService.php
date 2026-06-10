<?php
namespace App\Services;

class RoleService
{
    private const ROLES = [
        'Etudiant',
        'Enseignant',
        'Assistant',
        'Doyen',
        'Vice-Doyen',
        'Administrateur-Academique',
        'Administrateur',
        'Apparitaire',
    ];

    public static function getRoles(): array
    {
        return self::ROLES;
    }

    public static function normalize(string $role): string
    {
        $role = trim($role);
        $role = strtr($role, [
            'É' => 'E',
            'È' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
        ]);

        return str_replace([' ', '_'], '-', $role);
    }

    public static function isValidRole(string $role): bool
    {
        return in_array(self::normalize($role), self::ROLES, true);
    }

    public static function hasAccess(string $role, array $allowedRoles): bool
    {
        $normalizedRole = self::normalize($role);
        $normalizedAllowed = array_map([self::class, 'normalize'], $allowedRoles);
        return in_array($normalizedRole, $normalizedAllowed, true);
    }
}
