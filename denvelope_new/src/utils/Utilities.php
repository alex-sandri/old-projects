<?php

namespace Denvelope\Utils;

use Exception;

require(\dirname(__DIR__) . "/autoload.php");

use Denvelope\Config\Config;
use Denvelope\Config\Linguist;

use Denvelope\Database\DatabaseOperations;

require(\dirname(__FILE__, 3) . "/vendor/autoload.php");

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * @package Denvelope\Utils
 */
class Utilities
{
    private const BASE62_CHARSET = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    /**
     * Generates a random base 62 string of a given length
     * 
     * @param int ID Length
     * @return string Base 62 ID
     */
    public static function generateBase62(int $length = 25){
        $id = "";

        for($i = 0; $i < $length; $i++){
            $id .= self::BASE62_CHARSET[mt_rand(0, 61)];
        }

        return $id;
    }

    public static function generateUniqueId(string $table, string $id_column, int $length, string $type = "base62"){
        $unique_id = "";

        do{
            switch($type){
                case "base62":
                    $unique_id = self::generateBase62($length);
                    break;
                case "hex":
                default:
                    $unique_id = self::randomHex($length);
                    break;
            }
        }
        while(!self::isUnique($table, $id_column, $unique_id));

        return $unique_id;
    }

    public static function randomHex(int $length = 32){
        // Divide by two because a byte is equivalent to two hex values
        return \bin2hex(\random_bytes($length / 2));
    }

    public static function getNestedKey(array $array, string $key_path, string $delimiter = "->")
    {
        $keys = \explode($delimiter, $key_path);
        $keys_length = \count($keys);

        for ($i = 0; $i < $keys_length - 1; $i++) {
            $array = $array[$keys[$i]];
        }

        $value = $array[$keys[$keys_length - 1]];

        return $value;
    }

    /**
     * @param string $table
     * @param string $idColumn
     * @param string $id
     * @return bool
     */
    public static function isUnique(string $table, string $id_column, string $id){
        $result = DatabaseOperations::select([
            "columns" => [
                $id_column,
            ],
            "table" => $table,
            "filters" => [
                "where" => [
                    [
                        "field" => $id_column,
                        "value" => [
                            "identical" => $id,
                        ]
                    ]
                ]
            ]
        ]);

        return $result['num_rows'] === 0;
    }

    /**
     * @param array $filters
     * @return string
     */
    public static function toSQLFilters(array $filters){
        $q = "";

        if(\array_key_exists("where", $filters)){
            $q .= "WHERE";

            foreach ($filters['where'] as $where_filter) {
                $q .= " " . self::createSQLWhereFilter($where_filter);

                if(\array_key_exists("logic_op", $where_filter)){
                    $q .= " " . \strtoupper($where_filter['logic_op']);
                }
            }
        }

        if(\array_key_exists("order_by", $filters)){
            $q .= " ORDER BY " . $filters['order_by']['column'] . " " . $filters['order_by']['direction'];
        }
        
        // LIMIT and OFFSET need to be at the end of the query
        if(\array_key_exists("limit", $filters)){
            $q .= " LIMIT " . $filters['limit'];
        }

        if(\array_key_exists("offset", $filters)){
            $q .= " OFFSET " . $filters['offset'];
        }

        return \trim($q);
    }

    private static function createSQLWhereFilter(array $filter){
        $comparison_operator = "=";

        switch(\array_keys($filter['value'])[0]){
            case "not_equals":
                $comparison_operator = "!=";
                break;
            case "greater_than":
                $comparison_operator = ">";
                break;
            case "greater_than_or_equals":
                $comparison_operator = ">=";
                break;
            case "lower_than":
                $comparison_operator = "<";
                break;
            case "lower_than_or_equals":
                $comparison_operator = "<=";
                break;
            case "identical":
                $comparison_operator = "= BINARY ";
                break;
            case "like":
                $comparison_operator = " LIKE ";
            break;
            case "equals":
            default:
                break;
        }

        return $filter['field'] . $comparison_operator . "?";
    }

    public static function getQueryFiltersValues(array $filters){
        $params = array();

        // UPDATE COLUMNS VALUES need to be the first params in an UPDATE query
        if(\array_key_exists("update_columns_values", $filters)){
            foreach ($filters['update_columns_values'] as $update_column_value) {
                \array_push($params, $update_column_value);
            }
        }

        if(\array_key_exists("where", $filters)){
            foreach ($filters['where'] as $where_filter) {
                \array_push($params, $where_filter['value'][\array_keys($where_filter['value'])[0]]);
            }
        }

        return $params;
    }

    public static function AddSqlLikeCharacters (string $param, string $position) : string
    {
        switch ($position)
        {
            case "start":
                $param = "%" . $param;
            break;
            case "end":
                $param .= "%";
            break;
            case "both":
                $param = "%" . $param . "%";
            break;
            default:
                \http_response_code(500);
                exit();
            break;
        }

        return $param;
    }

    /**
     * @param string $db_lang The language identifier stored in the database, previously determined by the extension
     */
    public static function getLanguage(string $db_lang){
        return Linguist::get($db_lang);
    }

    public static function formatCurrency (string $cents) : string
    {
        $currency_symbol = Translation::getCurrency()['symbol'];

        $price = \substr($cents, 0, \strlen($cents) - 2) . Config::LANGUAGES[Translation::getCurrentLanguage()]['decimal_separator'] . \substr($cents, \strlen($cents) - 2);

        return $price . $currency_symbol;
    }

    public static function formatStorage (string $bytes, int $mode = 1000, int $precision = 0) : string
    {
        if ($mode !== 1024 && $mode !== 1000)
        {
            throw new Exception("Invalid rounding mode! Accepted values are: 1024 and 1000.");
        }

        $size = BigDecimal::of($bytes);

        $unit = "";

        for ($i = 0; $size->isGreaterThanOrEqualTo(BigDecimal::of($mode)); $i++)
        {
            $size = $size->dividedBy($mode, $precision, RoundingMode::HALF_DOWN);
        }

        switch($i){
            case 0:
                $unit = $mode === 1000
                    ? "B" // Byte
                    : "B" // Byte
                ;
                break;
            case 1:
                $unit = $mode === 1000
                    ? "KB" // KiloByte
                    : "KiB" // KibiByte
                ;
                break;
            case 2:
                $unit = $mode === 1000
                    ? "MB" // MegaByte
                    : "MiB" // MebiByte
                ;
                break;
            case 3:
                $unit = $mode === 1000
                    ? "GB" // GigaByte
                    : "GiB" // GibiByte
                ;
                break;
            case 4:
                $unit = $mode === 1000
                    ? "TB" // TeraByte
                    : "TiB" // TebiByte
                ;
                break;
            case 5:
                $unit = $mode === 1000
                    ? "PB" // PetaByte
                    : "PiB" // PebiByte
                ;
                break;
            case 6:
                $unit = $mode === 1000
                    ? "EB" // ExaByte
                    : "EiB" // ExbiByte
                ;
                break;
            case 7:
                $unit = $mode === 1000
                    ? "ZB" // ZettaByte
                    : "ZiB" // ZebiByte
                ;
                break;
            case 8:
                $unit = $mode === 1000
                    ? "YB" // YottaByte
                    : "YiB" // YobiByte
                ;
                break;
            default:
                break;
        }

        $size .= $unit;

        return $size;
    }
}