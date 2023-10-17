<?php

namespace Denvelope\Models;

require(\dirname(__DIR__) . "/autoload.php");

use Denvelope\Config\Config;

use Denvelope\Database\DatabaseInfo;
use Denvelope\Database\DatabaseOperations;

use Denvelope\Models\Cookie;

use Denvelope\Utils\Translation;
use Denvelope\Utils\Utilities;

/**
 * @package Denvelope\Models
 */
class Session
{
    public static function create(){
        $session_cookie = new Cookie(
            Config::SESSION_ID_COOKIE_NAME,
            Utilities::generateUniqueId(
                DatabaseInfo::SESSIONS_TABLE['table_name'],
                DatabaseInfo::SESSIONS_TABLE['columns']['token']["column_name"],
                Config::SESSION_ID_LENGTH,
                "hex"
            ),
            0
        );

        self::start([]);

        Translation::setCurrentLanguage("en-US");
    }

    public static function exists(){
        return Cookie::exists(Config::SESSION_ID_COOKIE_NAME);
    }

    public static function existsKey(string $key){
        $data = self::getAll();

        return \array_key_exists($key, $data);
    }

    public static function set(string $key, string $value){
        $data = self::getAll();

        $data[$key] = $value;

        self::update($data);
    }

    public static function get(string $key){
        $data = self::getAll();

        return self::existsKey($key)
            ? $data[$key]
            : null
        ;
    }

    private static function getToken(){
        return self::exists()
            ? Cookie::get(Config::SESSION_ID_COOKIE_NAME)
            : "PLACEHOLDER"
        ;
    }

    private static function start(array $data){
        DatabaseOperations::insert([
            "table" => DatabaseInfo::SESSIONS_TABLE['table_name'],
            "columns" => [
                DatabaseInfo::SESSIONS_TABLE['columns']['token']["column_name"],
                DatabaseInfo::SESSIONS_TABLE['columns']['data']["column_name"],
                DatabaseInfo::SESSIONS_TABLE['columns']['expires']["column_name"],
            ],
            "values" => [
                self::getToken(),
                \serialize($data),
                \time() + Config::SESSION_DURATION
            ]
        ]);
    }

    private static function update(array $data){
        DatabaseOperations::update([
            "table" => DatabaseInfo::SESSIONS_TABLE['table_name'],
            "columns" => [
                DatabaseInfo::SESSIONS_TABLE['columns']['data']["column_name"],
            ],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::SESSIONS_TABLE['columns']['token']["column_name"],
                        "value" => [
                            "identical" => self::getToken()
                        ]
                    ]
                ],
                "update_columns_values" => [
                    \serialize($data)
                ]
            ]
        ]);

        self::deleteExpiredSessions();
    }

    private static function getAll(){
        $result = DatabaseOperations::select([
            "columns" => [
                "*"
            ],
            "table" => DatabaseInfo::SESSIONS_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::SESSIONS_TABLE['columns']['token']["column_name"],
                        "value" => [
                            "identical" => self::getToken()
                        ]
                    ]
                ]
            ]
        ]);

        return \unserialize($result['result'][
            DatabaseInfo::SESSIONS_TABLE['columns']['data']["column_name"]
        ]);
    }

    private static function deleteExpiredSessions(){
        DatabaseOperations::delete([
            "table" => DatabaseInfo::SESSIONS_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::SESSIONS_TABLE['columns']['expires']["column_name"],
                        "value" => [
                            "lower_than_or_equals" => \time()
                        ]
                    ]
                ]
            ]
        ]);
    }
}