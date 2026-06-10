<?php
namespace App\Core;

class Config
{
    private static ?array $settings = null;

    public static function load(string $path): void
    {
        if (self::$settings === null) {
            self::$settings = require $path;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (self::$settings === null) {
            throw new \RuntimeException('Configuration non chargée.');
        }

        $segments = explode('.', $key);
        $value = self::$settings;
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
