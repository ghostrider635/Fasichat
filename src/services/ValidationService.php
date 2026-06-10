<?php
namespace App\Services;

class ValidationService
{
    public static function sanitizeString(string $value): string
    {
        return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }

    public static function sanitizeEmail(string $value): string
    {
        return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }

    public static function validateDate(string $value): bool
    {
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        $errors = \DateTime::getLastErrors();
        $validErrors = $errors === false || (($errors['warning_count'] ?? 0) === 0 && ($errors['error_count'] ?? 0) === 0);
        return $date !== false && $validErrors && $date->format('Y-m-d') === $value;
    }

    public static function validateTime(string $value): bool
    {
        $time = \DateTime::createFromFormat('H:i', $value);
        $errors = \DateTime::getLastErrors();
        $validErrors = $errors === false || (($errors['warning_count'] ?? 0) === 0 && ($errors['error_count'] ?? 0) === 0);
        return $time !== false && $validErrors && $time->format('H:i') === $value;
    }

    public static function sanitizeInteger(mixed $value): int
    {
        return filter_var($value, FILTER_VALIDATE_INT) ?: 0;
    }
}
