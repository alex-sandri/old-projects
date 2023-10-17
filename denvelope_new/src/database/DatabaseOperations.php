<?php

namespace Denvelope\Database;

use PDO;
use Exception;

require(\dirname(__FILE__, 2) . "/autoload.php");

use Denvelope\Database\Database;

use Denvelope\Utils\Crypto;
use Denvelope\Utils\Utilities;

class DatabaseOperations
{
    private static $conn = null;
    private static $stmt = null;

    public static function delete (array $params) : void
    {
        self::connect();

        if(\array_key_exists("filters", $params))
        {
            $sql_filters = " " . Utilities::toSQLFilters($params['filters']);
        }
        else
        {
            throw new Exception("Table deletion attempt blocked");
        }

        $q = "DELETE FROM " . $params['table'] . $sql_filters . ";";

        self::prepare($q);

        self::bindParam(Utilities::getQueryFiltersValues($params['filters']));

        self::execute();
    }

    public static function insert (array $params) : void
    {
        self::connect();

        $q = "INSERT INTO " . $params['table'] . " (" . \implode(", ", $params['columns']) . ") VALUES (" . \substr($temp = \str_repeat("?, ", \count($params['values'])), 0, \strlen($temp) - 2) . ");";

        self::prepare($q);

        self::bindParam($params['values']);

        self::execute();
    }

    public static function select (array $params) : array
    {
        self::connect();

        $sql_filters = "";

        if(\array_key_exists("filters", $params))
        {
            $sql_filters = " " . Utilities::toSQLFilters($params['filters']);
        }

        $q = "SELECT " . \implode(", ", $params['columns']) . " FROM " . $params['table'] . $sql_filters . ";";

        self::prepare($q);

        if(\array_key_exists("filters", $params))
        {
            self::bindParam(Utilities::getQueryFiltersValues($params['filters']));
        }

        self::execute();

        return array(
            "result" => \array_key_exists("result", $params)
                ? $params['result']['count'] === "first"
                    ? self::getFirstResult()
                    : self::getAllResults()
                : self::getFirstResult()
            ,
            "num_rows" => self::getRowCount()
        );
    }

    public static function update (array $params) : void
    {
        self::connect();

        $sql_filters = "";

        if(\array_key_exists("filters", $params))
        {
            $sql_filters = " " . Utilities::toSQLFilters($params['filters']);
        }

        $q = "UPDATE " . $params['table'] . " SET " . \implode("=?,", $params['columns']) . "=?" . $sql_filters . ";";

        self::prepare($q);

        if(\array_key_exists("filters", $params))
        {
            self::bindParam(Utilities::getQueryFiltersValues($params['filters']));
        }

        self::execute();
    }

    private static function connect() : void
    {
        if(self::$conn === null){
            self::$conn = Database::connect();
        }
    }

    private static function prepare (string $q) : void
    {
        self::$stmt = self::$conn->prepare($q);
    }

    private static function bindParam (array $values) : void
    {
        $num_of_values = \count($values);
        $encrypted_values = array();

        for ($i = 0; $i < $num_of_values; $i++) { 
            $encrypted_values[$i] = Crypto::encrypt($values[$i]);
            
            self::$stmt->bindParam($i + 1, $encrypted_values[$i], PDO::PARAM_STR);
        }
    }

    private static function execute () : void
    {
        self::$stmt->execute();
    }

    private static function getAllResults () : array
    {
        $results = self::$stmt->fetchAll(PDO::FETCH_ASSOC);
        $result_count = \count($results);

        for($i = 0; $i < $result_count; $i++){
            $results[$i] = $results[$i] !== false
                ? Crypto::decryptArray($results[$i])
                : $results[$i]
            ;
        }

        return $results;
    }

    private static function getFirstResult () : array
    {
        $first_result = self::$stmt->fetch(PDO::FETCH_ASSOC);

        return $first_result !== false
            ? Crypto::decryptArray($first_result)
            : []
        ;
    }

    private static function getRowCount () : int
    {
        return self::$stmt->rowCount();
    }
}