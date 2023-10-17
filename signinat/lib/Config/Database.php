<?php

namespace SignInAt\Config;

use PDO;
use PDOException;

/**
 * @package SignInAt\Config
 */
class Database {
    /**
     * @var string DB_HOST The database host
     * @var string DB_NAME The database name
     * @var string DB_USERNAME The database username
     * @var string DB_PASSWORD The database password
     */
    private const DB_HOST = "localhost";
    private const DB_NAME = "signinat";
    private const DB_USERNAME = "root";
    private const DB_PASSWORD = "";

    /**
     * @var PDO $conn Database Connection
     */
    private static $conn;

    /**
     * @return PDO Database Connection
     */
    public static function connect(){
        self::$conn = null;

        try {
            self::$conn = new PDO("mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME, self::DB_USERNAME, self::DB_PASSWORD);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }

        return self::$conn;
    }
}