<?php

namespace SignInAt\Util;

require(\dirname(__DIR__, 1) . "/autoload.php");

use SignInAt\DatabaseOperations\DatabaseOperations;

class Utilities {
    private const BASE62_CHARSET = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    /**
     * Generates a random base 62 string of a given length
     * 
     * @param int ID Length
     * @return string Base 62 ID
     */
    public static function base62RandomID(int $length = 25){
        $id = "";

        for($i = 0; $i < $length; $i++){
            $id .= self::BASE62_CHARSET[mt_rand(0, 61)];
        }

        return $id;
    }

    /**
     * Hashes a given password
     * 
     * @param string Password to hash
     * @return string Hashed password
     */
    public static function hashPassword(string $password){
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param string $table
     * @param string $idColumn
     * @param string $id
     * @return bool
     */
    public static function uniqueID(string $table, string $idColumn, string $id){
        $result = DatabaseOperations::select(
            array(
                $idColumn
            ),
            $table,
            array(
                "WHERE" => [
                    0 => [
                        "field" => $idColumn,
                        "value" => $id,
                        "type" => "s"
                    ]
                ]
            )
        );

        if($result['num_rows'] > 0){
            return false;
        }
        else{
            return true;
        }
    }

    /**
     * @param array $options
     * @return array
     * 
     * SAMPLE $options:
     * 
     * $options = array(
     *      "WHERE" => [
     *          0 => [
     *              "field" => "username",
     *              "value" => "test",
     *              "type" => "s"
     *          ],
     *          "condition" => "AND",
     *          1 => [
     *              "field" => "email",
     *              "value" => "test@test.test",
     *              "type" => "s"
     *          ],
     *      ],
     *      "ORDER BY" => "id",
     *      "LIMIT" => 1000,
     * );
     */
    public static function toSQLFilters(array $options){
        $q = "";
        $params = array();

        if(\array_key_exists("WHERE", $options)){
            $q .= "WHERE";

            foreach ($options['WHERE'] as $key => $value) {
                if(\is_numeric($key)){
                    $q .= " " . $options['WHERE'][$key]['field'] . "=?";

                    $params[$key]['value'] = $options['WHERE'][$key]['value'];
                    $params[$key]['type'] = $options['WHERE'][$key]['type'];
                }
                else{
                    $q .= " " . $options['WHERE']['condition'];
                }
            }
        }
        if(\array_key_exists("ORDER BY", $options)){
            $q .= \strlen($q) == 0 ? "ORDER BY" : " ORDER BY" . " " . $options['ORDER BY'];
        }
        if(\array_key_exists("LIMIT", $options)){
            $q .= \strlen($q) == 0 ? "LIMIT" : " LIMIT" . " " . $options['LIMIT'];
        }

        return array(
            "query" => $q,
            "params" => \array_map(function($param){
                return array(
                    "value" => $param['value'],
                    "type" => $param['type']
                );
            }, $params)
        );
    }
}