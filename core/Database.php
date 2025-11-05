<?php
class Database {
    private static ?\PDO $pdo = null;

    public static function getConnection(): \PDO {
        if (self::$pdo) return self::$pdo;
        $config = require __DIR__ . '/../Config/config.php';
        $db = $config['db'];
        $dsn = '';
        if ($db['driver'] === 'pgsql') {
            $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['database']}";
        } else {
            $charset = 'utf8mb4';
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset={$charset}";
        }
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ];
        self::$pdo = new \PDO($dsn, $db['username'], $db['password'], $options);
        return self::$pdo;        
    }

    public static function driver(): string {
        $config = require __DIR__ . '/../Config/config.php';
        return $config['db']['driver'];
    }

    public static function schema(): ?string {
        $config = require __DIR__ . '/../Config/config.php';
        return $config['db']['schema'] ?? null;
    }
}