<?php

namespace SignInAt\Util;

require(\dirname(__DIR__, 1) . "/autoload.php");

use SignInAt\Config\Config;
use SignInAt\Config\DatabaseInfo;
use SignInAt\DatabaseOperations\DatabaseOperations;

class Validate {
    public static function username(string $username){
        $valid;
        $error_msg = Config::DEFAULT_INPUT_VALIDATION_ERROR_MSG;

        if(empty($username)){
            $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["EMPTY"];
        }
        else if(\strlen($username) < Config::USERNAME_MIN_LENGTH){
            $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["SHORT"];
        }
        else if(\strlen($username) > Config::USERNAME_MAX_LENGTH){
            $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["LONG"];
        }
        else if(!\preg_match(Config::USERNAME_REGEX, $username)){
            $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["INVALID"];
        }
        else{
            $result = DatabaseOperations::select(
                array(
                    "*"
                ),
                DatabaseInfo::USER_TABLE_NAME,
                array(
                    "WHERE" => [
                        0 => [
                            "field" => DatabaseInfo::USER_TABLE_COLUMNS["USERNAME"],
                            "value" => $username,
                            "type" => "s"
                        ]
                    ]
                )
            );

            if($result["num_rows"] > 0){
                $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["TAKEN"];
            }
        }

        $valid = $error_msg === Config::DEFAULT_INPUT_VALIDATION_ERROR_MSG;

        return array(
            "valid" => $valid,
            "error_msg" => $error_msg,
        );
    }

    public static function email(string $email){
        $valid;
        $error_msg = Config::DEFAULT_INPUT_VALIDATION_ERROR_MSG;

        if(empty($email)){
            $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["EMPTY"];
        }
        else if(\strlen($email) > Config::EMAIL_MAX_LENGTH){
            $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["LONG"];
        }
        else if(!\filter_var($email, FILTER_VALIDATE_EMAIL)){
            $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["INVALID"];
        }
        else{
            $result = DatabaseOperations::select(
                array(
                    "*"
                ),
                DatabaseInfo::USER_TABLE_NAME,
                array(
                    "WHERE" => [
                        0 => [
                            "field" => DatabaseInfo::USER_TABLE_COLUMNS["EMAIL"],
                            "value" => $email,
                            "type" => "s"
                        ]
                    ]
                )
            );

            if($result["num_rows"] > 0){
                $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["TAKEN"];
            }
        }

        $valid = $error_msg === Config::DEFAULT_INPUT_VALIDATION_ERROR_MSG;

        return array(
            "valid" => $valid,
            "error_msg" => $error_msg,
        );
    }

    public static function password(string $password){
        $valid;
        $error_msg = Config::DEFAULT_INPUT_VALIDATION_ERROR_MSG;

        if(empty($password)){
            $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["EMPTY"];
        }
        else if(\strlen($password) < Config::PASSWORD_MIN_LENGTH){
            $error_msg = Config::INPUT_VALIDATION_ERROR_MSG["SHORT"];
        }

        $valid = $error_msg === Config::DEFAULT_INPUT_VALIDATION_ERROR_MSG;

        return array(
            "valid" => $valid,
            "error_msg" => $error_msg,
        );
    }
}