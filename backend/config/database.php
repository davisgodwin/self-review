<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? 5432;
            $db   = $_ENV['DB_DATABASE'] ?? 'defaultdb';
            $user = $_ENV['DB_USERNAME'] ?? 'postgres';
            $pass = $_ENV['DB_PASSWORD'] ?? '';
            $sslmode = $_ENV['DB_SSLMODE'] ?? 'require';

            $dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode={$sslmode}";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Database connection failure: " . $e->getMessage()]);
                exit;
            }
        }
        return self::$instance;
    }
}