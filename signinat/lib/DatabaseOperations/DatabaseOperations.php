<?php

namespace SignInAt\DatabaseOperations;

require(\dirname(__DIR__, 1) . "/autoload.php");

use SignInAt\Config\Database;
use SignInAt\Util\Utilities;

use PDO;

class DatabaseOperations {
    private static $conn;
    private static $stmt;

    /**
     * @param string $table The table to insert data in
     * @param array $columns The columns to insert each value in
     * @param string $types The types of each value
     * @param array $values The values to insert
     */
    public static function insert(string $table, array $columns, string $types, array $values){
        self::connect();

        $q = "INSERT INTO $table (" . \implode(", ", $columns) . ") VALUES (" . \substr($temp = \str_repeat("?, ", \count($values)), 0, \strlen($temp) - 2) . ");";

        self::prepare($q);

        self::bindParam($types, $values);

        self::execute();
    }

    public static function select(array $columns, string $table, array $options = null){
        self::connect();

        if($options != null){
            $options = Utilities::toSQLFilters($options);
        }

        $q = "SELECT " . \implode(", ", $columns) . " FROM $table" . ($options !== null ? " " . $options["query"] : "") . ";";

        self::prepare($q);

        $types = "";
        $values = array();

        if($options !== null){
            foreach ($options['params'] as $param) {
                $types .= $param['type'];
                \array_push($values, $param['value']);
            }
    
            self::bindParam($types, $values);
        }

        self::execute();

        return array(
            "result" => self::result(),
            "assoc_result" => self::assocResult(),
            "num_rows" => self::rowCount()
        );
    }

    private static function connect(){
        self::$conn = Database::connect();
    }

    private static function prepare(string $q){
        self::$stmt = self::$conn->prepare($q);
    }

    private static function bindParam(string $types, array $values){
        $i = 0;

        foreach ($values as $value) {
            self::$stmt->bindParam($i + 1, $values[$i], self::PDOType($types[$i]));

            $i++;
        }
    }

    private static function execute(){
        self::$stmt->execute();
    }

    private static function result(){
        return self::$stmt->fetchAll();
    }

    private static function assocResult(){
        return self::$stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function rowCount(){
        return self::$stmt->rowCount();
    }

    private static function PDOType(string $type){
        switch ($type) {
            case 'i':
                $type = PDO::PARAM_INT;
                break;
            default:
                $type = PDO::PARAM_STR;
                break;
        }

        return $type;
    }
}