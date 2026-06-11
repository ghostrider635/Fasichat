<?php
namespace App\Services;

class PasswordValidator
{
    private const MIN_LENGTH = 12;
    private const REQUIRE_UPPERCASE = true;
    private const REQUIRE_LOWERCASE = true;
    private const REQUIRE_NUMBERS = true;
    private const REQUIRE_SPECIAL_CHARS = true;
    
    private static $commonPasswords = [
        'password', 'password123', '123456', '12345678', '123456789',
        'qwerty', 'abc123', 'admin', 'letmein', 'welcome',
        'monkey', 'dragon', 'football', 'baseball', 'master'
    ];
    
    public static function validate(string $password): array
    {
        $errors = [];
        
        // Vérifier la longueur
        if (strlen($password) < self::MIN_LENGTH) {
            $errors[] = "Le mot de passe doit contenir au moins " . self::MIN_LENGTH . " caractères";
        }
        
        // Vérifier les exigences
        if (self::REQUIRE_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre majuscule";
        }
        
        if (self::REQUIRE_LOWERCASE && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre minuscule";
        }
        
        if (self::REQUIRE_NUMBERS && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre";
        }
        
        if (self::REQUIRE_SPECIAL_CHARS && !preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial (!@#$%^&*() etc.)";
        }
        
        // Vérifier les mots de passe courants
        $lowerPassword = strtolower($password);
        foreach (self::$commonPasswords as $common) {
            if (strpos($lowerPassword, $common) !== false) {
                $errors[] = "Le mot de passe est trop commun ou contient un mot de passe courant";
                break;
            }
        }
        
        // Vérifier les séquences simples
        if (preg_match('/(.)\1{3,}/', $password)) {
            $errors[] = "Le mot de passe contient trop de caractères identiques consécutifs";
        }
        
        // Vérifier les séquences numériques
        if (preg_match('/12345|23456|34567|45678|56789|67890/', $password)) {
            $errors[] = "Le mot de passe contient une séquence numérique trop simple";
        }
        
        // Vérifier les séquences de clavier
        $keyboardSequences = ['qwerty', 'asdfgh', 'zxcvbn', 'azerty', 'qwertz'];
        foreach ($keyboardSequences as $seq) {
            if (strpos($lowerPassword, $seq) !== false) {
                $errors[] = "Le mot de passe contient une séquence de clavier trop commune";
                break;
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'score' => self::calculateStrength($password)
        ];
    }
    
    public static function calculateStrength(string $password): int
    {
        $score = 0;
        
        // Longueur
        $length = strlen($password);
        if ($length >= 12) $score += 3;
        elseif ($length >= 10) $score += 2;
        elseif ($length >= 8) $score += 1;
        
        // Diversité des caractères
        $charTypes = 0;
        if (preg_match('/[a-z]/', $password)) $charTypes++;
        if (preg_match('/[A-Z]/', $password)) $charTypes++;
        if (preg_match('/[0-9]/', $password)) $charTypes++;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $charTypes++;
        
        $score += ($charTypes - 1) * 2;
        
        // Entropie (estimation)
        $entropy = log(pow(94, $length)) / log(2);
        if ($entropy > 80) $score += 3;
        elseif ($entropy > 60) $score += 2;
        elseif ($entropy > 40) $score += 1;
        
        // Pénalités pour motifs simples
        if (preg_match('/(.)\1{2,}/', $password)) $score -= 2;
        if (preg_match('/^\d+$/', $password)) $score -= 3;
        
        return min(max($score, 0), 10);
    }
    
    public static function generateStrongPassword(int $length = 16): string
    {
        $chars = [
            'lower' => 'abcdefghijklmnopqrstuvwxyz',
            'upper' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'numbers' => '0123456789',
            'special' => '!@#$%^&*()-_=+[]{}|;:,.<>?'
        ];
        
        $password = '';
        
        // Assurer au moins un caractère de chaque type
        $password .= $chars['lower'][random_int(0, strlen($chars['lower']) - 1)];
        $password .= $chars['upper'][random_int(0, strlen($chars['upper']) - 1)];
        $password .= $chars['numbers'][random_int(0, strlen($chars['numbers']) - 1)];
        $password .= $chars['special'][random_int(0, strlen($chars['special']) - 1)];
        
        // Remplir le reste
        $allChars = implode('', $chars);
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Mélanger pour éviter les motifs
        return str_shuffle($password);
    }
}