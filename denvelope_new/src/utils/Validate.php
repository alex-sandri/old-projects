<?php

namespace Denvelope\Utils;

require(\dirname(__DIR__, 1) . "/autoload.php");

use Denvelope\Api\ApiError;
use Denvelope\Config\Config;

use Denvelope\Database\DatabaseInfo;
use Denvelope\Database\DatabaseOperations;

class Validate
{
    public static function Username (string $username) : array
    {
        $error = ApiError::NONE;

        if (empty($username)) $error = ApiError::EMPTY;
        else if (\strlen($username) < Config::USERNAME_MIN_LENGTH) $error = ApiError::SHORT;
        else if (\strlen($username) > Config::USERNAME_MAX_LENGTH) $error = ApiError::LONG;
        else if (!\preg_match(Config::USERNAME_REGEX, $username)) $error = ApiError::INVALID;
        else
        {
            $result = DatabaseOperations::select([
                "columns" => [
                    "*"
                ],
                "table" => DatabaseInfo::USERS_TABLE["table_name"],
                "filters" => [
                    "where" => [
                        [
                            "field" => DatabaseInfo::USERS_TABLE["columns"]["username"]["column_name"],
                            "value" => [
                                "equals" => $username
                            ]
                        ]
                    ]
                ]
            ]);

            if ($result["num_rows"] > 0) $error = ApiError::TAKEN;
        }

        return
        [
            "valid" => $error === ApiError::NONE,
            "error" => $error,
        ];
    }

    public static function Email (string $email) : array
    {
        $error = ApiError::NONE;

        if (empty($email)) $error = ApiError::EMPTY;
        else if (\strlen($email) > Config::EMAIL_MAX_LENGTH) $error = ApiError::LONG;
        else if (!\filter_var($email, FILTER_VALIDATE_EMAIL)) $error = ApiError::INVALID;
        else
        {
            $result = DatabaseOperations::select([
                "columns" => [
                    "*"
                ],
                "table" => DatabaseInfo::TABLES['users']['table_name'],
                "filters" => [
                    "where" => [
                        [
                            "field" => DatabaseInfo::TABLES['users']['columns']['email']["column_name"],
                            "value" => [
                                "equals" => $email
                            ]
                        ]
                    ]
                ]
            ]);

            if ($result["num_rows"] > 0) $error = ApiError::TAKEN;
        }

        return
        [
            "valid" => $error === ApiError::NONE,
            "error" => $error,
        ];
    }

    public static function Password (string $password) : array
    {
        $error = ApiError::NONE;

        if (empty($password)) $error = ApiError::EMPTY;
        else if (\strlen($password) < Config::PASSWORD_MIN_LENGTH) $error = ApiError::SHORT;

        return
        [
            "valid" => $error === ApiError::NONE,
            "error" => $error,
        ];
    }

    public static function usernameEmail(string $username_email){
        $valid = true;
        $error = "no_errors";

        if(empty($username_email)){
            $error = "empty";
        }
        else if(
            (
                \strlen($username_email) > Config::USERNAME_MAX_LENGTH
                AND
                \strlen($username_email) > Config::EMAIL_MAX_LENGTH
            )
            OR
            (
                !($username_validation = self::username($username_email))['valid']
                AND
                !($email_validation = self::email($username_email))['valid']
            )
        ){
            if(
                $username_validation['error'] !== "taken"
                AND
                $email_validation['error'] !== "taken"
            ){
                $error = "invalid";
            }
        }
        else if(
            $username_validation['error'] !== "taken"
            OR
            $email_validation['error'] !== "taken"
        ){
            $error = "not_exists";
        }

        $valid = $error === "no_errors";

        return array(
            "valid" => $valid,
            "error" => $error,
        );
    }

    public static function preferredLanguage(string $preferred_language){
        $valid = true;
        $error_type = "no_errors";

        if(empty($preferred_language)){
            $error_type = "empty";
        }
        else{
            switch($preferred_language){
                case "en-US":
                case "it-IT":
                    break;
                default:
                    $error_type = "invalid";
                    break;
            }
        }

        $valid = $error_type === "no_errors";

        return array(
            "valid" => $valid,
            "error_type" => $error_type,
        );
    }

    public static function checkbox (string $value)
    {
        $valid = true;
        $error_type = "no_errors";

        if(empty($value)){
            $error_type = "empty";
        }
        else if ($value !== DatabaseInfo::DATA_TYPES['bool']['values']['true'] && $value !== DatabaseInfo::DATA_TYPES['bool']['values']['false']){
            $error_type = "invalid";
        }

        $valid = $error_type === "no_errors";

        return array(
            "valid" => $valid,
            "error_type" => $error_type,
        );
    }
}