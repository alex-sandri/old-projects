<?php

namespace Denvelope\Database;

require(\dirname(__FILE__, 2) . "/autoload.php");

use PDO;
use PDOException;

/**
 * @package Denvelope\Database
 */
class Database
{
    private const DB_HOST = "localhost";
    private const DB_NAME = "denvelope_new";
    private const DB_USERNAME = "root";
    private const DB_PASSWORD = "";

    /**
     * @var null|PDO $conn Database Connection
     */
    private static $conn = null;

    /**
     * @return PDO Database Connection
     */
    public static function connect () : PDO
    {
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