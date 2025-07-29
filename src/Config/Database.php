<?php

declare(strict_types=1);

namespace App\Config;

use PDO;

class Database
{
    private static ?PDO $instance = null;
    public static function getConnection(): PDO
    {
        if (self::$instance === null)
        {
            $host = '';
            $dbname = '';
            $username = '';
            $password = '';
            $charset = 'utf8mb4';
            $dns = "mysql:host={$host};dname={$dbname};charset={$charset}";
            $option = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            self::$instance = new PDO($dns, $username, $password, $option);
        }
        return self::$instance;
    }
}