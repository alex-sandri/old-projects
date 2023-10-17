<?php

namespace Denvelope\Models;

require(\dirname(__DIR__) . "/autoload.php");

use Denvelope\Config\Config;
use Denvelope\Database\DatabaseInfo;
use Denvelope\Database\DatabaseOperations;
use Denvelope\Models\Cookie;
use Denvelope\Models\Session;
use Denvelope\Models\User;
use Denvelope\Utils\Utilities;

/**
 * @package Denvelope\Models
 */
class UserSession
{
    public static function create(string $token, string $user_id){
        $result = DatabaseOperations::insert([
            "table" => DatabaseInfo::TABLES['user_sessions']['table_name'],
            "columns" => [
                DatabaseInfo::TABLES['user_sessions']['columns']['token']["column_name"],
                DatabaseInfo::TABLES['user_sessions']['columns']['user_id']["column_name"]
            ],
            "values" => [
                $token,
                $user_id
            ]
        ]);

        return $result;
    }

    public static function get(string $token){
        $result = DatabaseOperations::select([
            "columns" => [
                "*"
            ],
            "table" => DatabaseInfo::TABLES['user_sessions']['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" =>  DatabaseInfo::TABLES['user_sessions']['columns']['token']["column_name"],
                        "value" => [
                            "identical" => $token 
                        ]
                    ]
                ]
            ]
        ]);

        return $result;
    }

    public static function Delete ()
    {
        $result = DatabaseOperations::delete([
            "table" => DatabaseInfo::USER_SESSIONS_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::USER_SESSIONS_TABLE['columns']['token']["column_name"],
                        "value" => [
                            "identical" => self::getToken()
                        ]
                    ]
                ]
            ]
        ]);
    }

    public static function isValid(){
        $is_valid = true;

        if(!Cookie::exists(Config::USER_SESSION_COOKIE_NAME)){
            $is_valid = false;
        }
        else{
            $session = self::get(
                self::getToken()
            );

            if($session['num_rows'] === 0){
                Cookie::delete(
                    Config::USER_SESSION_COOKIE_NAME
                );
    
                $is_valid = false;
            }
        }

        return $is_valid;
    }

    public static function getUser(){
        $session = self::get(self::getToken())['result'];
        
        $user = User::Retrieve([
            "value" => $session[DatabaseInfo::USER_SESSIONS_TABLE['columns']['user_id']["column_name"]],
            "search_by" => DatabaseInfo::USER_SESSIONS_TABLE['columns']['user_id']["column_name"],
        ]);

        return $user;
    }

    public static function RetrieveUserId ()
    {
        return self::getUser()[DatabaseInfo::USERS_TABLE['columns']['id']["column_name"]];
    }

    private static function getToken(){
        return Cookie::get(
            Config::USER_SESSION_COOKIE_NAME
        );
    }
}