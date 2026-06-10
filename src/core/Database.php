<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/app.php';
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['db']['host'], $config['db']['name'], $config['db']['charset']);
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $options);
            } catch (PDOException $e) {
                throw new \RuntimeException('Impossible de se connecter à la base de données.');
            }
        }
        return self::$instance;
    }
}
