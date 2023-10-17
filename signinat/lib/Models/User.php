<?php

namespace SignInAt\Models;

require(\dirname(__DIR__, 1) . "/autoload.php");

use SignInAt\Config\Config;
use SignInAt\Config\DatabaseInfo;
use SignInAt\DatabaseOperations\DatabaseOperations;
use SignInAt\Util\Utilities;
use SignInAt\Util\Validate;

/**
 * @package SignInAt\Models
 */
class User {
    /**
     * @var string|null $id The user's unique ID
     * @var string|null $username The user's username
     * @var array|null $email The user's email
     * @var string|null $password The user's password in plain text (hashed and deleted after validation)
     * @var string|null $hashed_password The user's hashed password
     * @var array|null $apps The apps the user has an account with
     */
    private static $id = null;
    private static $username = null;
    private static $email = null;
    private static $password = null;
    private static $hashed_password = null;
    private static $apps = null;

    /**
     * @var bool $valid_input Checks whether the user's input is correct or not
     * @var array $username_validation The username validation response
     * @var array $email_validation The email validation response
     * @var array $password_validation The password validation response
     */
    private static $valid_input;
    private static $username_validation, $email_validation, $password_validation;

    /**
     * Adds the user to an app
     */
    public static function add(string $user_id, string $app_id){
        $user = self::retrieve($user_id);
    }

    /**
     * Creates a new user
     *
     * @param string $username The user's username
     * @param string $email The user's email
     * @param string $password The user's password
     * @return array User creation response
     */
    public static function create(string $username, string $email, string $password){
        self::$username = $username;
        self::$email = $email;
        self::$password = $password;
        self::$hashed_password = Utilities::hashPassword($password);

        self::setUserID();

        self::validateInput();

        if(self::$valid_input){
            self::register();
        }

        return array(
            "action" => [
                "type" => Config::API_ACTIONS["USER_CREATE"],
                "success" => self::$valid_input,
                "errors" => [
                    "username" => self::$username_validation["error_msg"],
                    "email" => self::$email_validation["error_msg"],
                    "password" => self::$password_validation["error_msg"]
                ]
            ],
            "data" => [
                "id" => self::$id,
                "username" => self::$username,
                "email" => self::$email
            ]
        );
    }

    /**
     * Retrieves an user based on the user ID
     *
     * @return array The user
     */
    public static function retrieve(string $user_id){
        return DatabaseOperations::select(
            array(
                "*"
            ),
            DatabaseInfo::USER_TABLE_NAME,
            array(
                "WHERE" => [
                    0 => [
                        "field" => DatabaseInfo::USER_TABLE_COLUMNS["ID"],
                        "value" => $user_id,
                        "type" => "s"
                    ]
                ]
            ),
        );
    }

    /**
     * Registers the user on the Database
     */
    private static function register(){
        DatabaseOperations::insert(
            DatabaseInfo::USER_TABLE_NAME,
            array(
                DatabaseInfo::USER_TABLE_COLUMNS["ID"],
                DatabaseInfo::USER_TABLE_COLUMNS["USERNAME"],
                DatabaseInfo::USER_TABLE_COLUMNS["EMAIL"],
                DatabaseInfo::USER_TABLE_COLUMNS["PASSWORD"],
            ),
            "ssss",
            array(
                self::$id,
                self::$username,
                self::$email,
                self::$hashed_password,
            )
        );
    }

    /**
     * Sets the user ID
     */
    private static function setUserID(){
        do{
            self::$id = Utilities::base62RandomID(Config::USER_ID_LENGTH);
        }
        while(!Utilities::uniqueID(DatabaseInfo::USER_TABLE_NAME, DatabaseInfo::USER_TABLE_COLUMNS["ID"], self::$id));
    }

    /**
     * Validates the user's input
     */
    private static function validateInput(){
        self::$username_validation = Validate::username(self::$username);
        self::$email_validation = Validate::email(self::$email);
        self::$password_validation = Validate::password(self::$password);

        self::$valid_input = (
            self::$username_validation["valid"]
            AND
            self::$email_validation["valid"]
            AND
            self::$password_validation["valid"]
        );

        self::$password = null;
    }
}
