<?php

namespace Denvelope\Models;

use Exception;

require(\dirname(__DIR__) . "/autoload.php");

use Denvelope\Api\Api;
use Denvelope\Api\ApiError;
use Denvelope\Api\ApiObject;
use Denvelope\Api\ApiResponse;
use Denvelope\Api\ApiStatus;

use Denvelope\Config\Config;

use Denvelope\Database\DatabaseInfo;
use Denvelope\Database\DatabaseOperations;

use Denvelope\Interfaces\ApiInterface;

use Denvelope\Utils\Crypto;
use Denvelope\Utils\Translation;
use Denvelope\Utils\Utilities;
use Denvelope\Utils\Validate;

class User implements ApiInterface
{
    public static function Create (array $data) : array
    {
        if (!\array_key_exists("username", $data) || !\array_key_exists("email", $data) || !\array_key_exists("password", $data)) Api::SetStatus(ApiStatus::BAD_REQUEST);

        $username = $data["username"];
        $email = $data["email"];
        $password = $data["password"];

        $id = Utilities::generateUniqueId(DatabaseInfo::USERS_TABLE["table_name"], DatabaseInfo::USERS_TABLE["columns"]["id"]["column_name"], Config::USER_ID_LENGTH, "base62");

        $status = ApiStatus::OK;

        $errors = [];

        if (!($validation = Validate::Username($username))["valid"])
        {
            $errors["username"] = [
                "error" => $error = $validation["error"],
                "message" => Translation::Retrieve([
                    "id" => "api->messages->user->username->$error"
                ])["data"]["translation"]
            ];
        }

        if (!($validation = Validate::Username($email))["valid"])
        {
            $errors["email"] = [
                "error" => $error = $validation["error"],
                "message" => Translation::Retrieve([
                    "id" => "api->messages->user->email->$error"
                ])["data"]["translation"]
            ];
        }

        if (!($validation = Validate::Username($password))["valid"])
        {
            $errors["username"] = [
                "error" => $error = $validation["error"],
                "message" => Translation::Retrieve([
                    "id" => "api->messages->user->password->$error"
                ])["data"]["translation"]
            ];
        }

        if (count($errors) === 0) self::Register($id, $username, $email, Crypto::HashPassword($password));
        else $status = ApiStatus::FORBIDDEN;

        $response = new ApiResponse(ApiObject::USER, $status, [], $errors);

        return $response->__serialize();
    }

    public static function Authenticate (array $data) : array
    {
        if (!\array_key_exists("username_email", $data) || !\array_key_exists("password", $data)) Api::SetStatus(ApiStatus::BAD_REQUEST);

        $username_email = $data["username_email"];
        $password = $data["password"];

        $status = ApiStatus::OK;

        $errors = [];

        if (!($validation = Validate::UsernameEmail($username_email))["valid"])
        {
            $errors["username_email"] = [
                "error" => $error = $validation["error"],
                "message" => Translation::Retrieve([
                    "id" => "api->messages->user->username_email->$error"
                ])["data"]["translation"]
            ];
        }

        if (!($validation = Validate::Password($password))["valid"])
        {
            $errors["password"] = [
                "error" => $error = $validation["error"],
                "message" => Translation::Retrieve([
                    "id" => "api->messages->user->password->$error"
                ])["data"]["translation"]
            ];
        }

        $account_activated = false;
    
        if (count($errors) === 0)
        {
            $user = self::Retrieve([
                "value" => $username_email,
                "search_by" => "username_email"
            ]);

            $account_activated = $user[DatabaseInfo::USERS_TABLE["columns"]["activated"]["column_name"]] !== DatabaseInfo::USERS_TABLE["columns"]["activated"]["default_value"];
        
            if (!Crypto::VerifyPassword($password, $user[DatabaseInfo::USERS_TABLE["columns"]["password"]["column_name"]]))
            {
                $errors["password"] = [
                    "error" => ApiError::WRONG,
                    "message" => Translation::Retrieve([
                        "id" => "api->messages->user->password->wrong"
                    ])["data"]["translation"]
                ];

                $status = ApiStatus::FORBIDDEN;
            }
            else
            {
                $session_coookie = new Cookie(
                    Config::USER_SESSION_COOKIE_NAME,
                    Utilities::generateUniqueId(
                        DatabaseInfo::USER_SESSIONS_TABLE["table_name"],
                        DatabaseInfo::USER_SESSIONS_TABLE["columns"]["token"]["column_name"],
                        Config::USER_SESSION_ID_LENGTH,
                        "hex"
                    )
                );
            
                UserSession::create(
                    $session_coookie->getValue(),
                    $user[DatabaseInfo::USERS_TABLE["columns"]["id"]["column_name"]]
                );
            }
        }
        else $status = ApiStatus::FORBIDDEN;

        $response = new ApiResponse(ApiObject::USER, $status, [
            "account" => [
                "activated" => $account_activated,
            ]
        ], $errors);

        return $response->__serialize();
    }

    public static function Retrieve (array $data) : array
    {
        if (!\array_key_exists("value", $data) || !\array_key_exists("search_by", $data)) Api::SetStatus(ApiStatus::BAD_REQUEST);

        $where_filters = [];

        switch ($data["search_by"])
        {
            case "user_id":
                $where_filters = [
                    [
                        "field" => DatabaseInfo::USERS_TABLE["columns"]["id"]["column_name"],
                        "value" => [
                            "identical" => $data['value']
                        ]
                    ]
                ];
            break;
            case "username_email":
                $where_filters = [
                    [
                        "field" => DatabaseInfo::USERS_TABLE["columns"]["username"]["column_name"],
                        "value" => [
                            "equals" => $data['value']
                        ],
                        "logic_op" => "or"
                    ],
                    [
                        "field" => DatabaseInfo::USERS_TABLE["columns"]["email"]["column_name"],
                        "value" => [
                            "equals" => $data['value']
                        ]
                    ]
                ];
            break;
            default:
                ApiStatus::BAD_REQUEST;
            break;
        }

        $result = DatabaseOperations::select([
            "columns" => [
                "*"
            ],
            "table" => DatabaseInfo::USERS_TABLE["table_name"],
            "filters" => [
                "where" => $where_filters
            ],
            "result" => [
                "count" => "first"
            ]
        ]);

        if ($result["num_rows"] === 0) Api::SetStatus(ApiStatus::NOT_FOUND);

        return $result["result"];
    }

    public static function ForgotPassword (array $data) : array
    {
        if (!\array_key_exists("username_email", $data)) Api::SetStatus(ApiStatus::BAD_REQUEST);

        $username_email = $data["username_email"];

        $status = ApiStatus::OK;

        $errors = [];

        if (!($validation = Validate::UsernameEmail($username_email))["valid"])
        {
            $errors["username_email"] = [
                "error" => $error = $validation["error"],
                "message" => Translation::Retrieve([
                    "id" => "api->messages->user->username_email->$error"
                ])["data"]["translation"]
            ];
        }

        if (count($errors) === 0)
        {
            $expires = time() + Config::PASSWORD_RESET_TOKEN_DURATION;

            $token = Utilities::randomHex(Config::PASSWORD_RESET_TOKEN_LENGTH);

            $result = self::Retrieve([
                "value" => $username_email,
                "search_by" => "username_email"
            ]);

            DatabaseOperations::delete([
                "table" => DatabaseInfo::PASSWORD_RESETS_TABLE["table_name"],
                "filters" => [
                    "where" => [
                        [
                            "field" => DatabaseInfo::PASSWORD_RESETS_TABLE["columns"]["id"]["column_name"],
                            "value" => [
                                "equals" => $result[DatabaseInfo::USERS_TABLE["columns"]["id"]["column_name"]]
                            ]
                        ]
                    ]
                ]
            ]);

            DatabaseOperations::insert([
                "table" => DatabaseInfo::PASSWORD_RESETS_TABLE['table_name'],
                "columns" => [
                    DatabaseInfo::PASSWORD_RESETS_TABLE['columns']['id']["column_name"],
                    DatabaseInfo::PASSWORD_RESETS_TABLE['columns']['token']["column_name"],
                    DatabaseInfo::PASSWORD_RESETS_TABLE['columns']['expires']["column_name"]
                ],
                "values" => [
                    $result[DatabaseInfo::USERS_TABLE['columns']['id']["column_name"]],
                    $token,
                    $expires
                ]
            ]);
        }
        else $status = ApiStatus::FORBIDDEN;

        $response = new ApiResponse(ApiObject::USER, $status, [], $errors);

        return $response->__serialize();
    }

    public static function Update (array $data) : array
    {
        if(!\array_key_exists("id", $data)) Api::SetStatus(ApiStatus::BAD_REQUEST);

        $id = $data["id"];

        unset($data["id"]);

        DatabaseOperations::update([
            "table" => DatabaseInfo::USERS_TABLE["table_name"],
            "columns" => \array_map(function ($column) {return DatabaseInfo::USERS_TABLE["columns"][$column]["column_name"];}, \array_keys($data)),
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::USERS_TABLE["columns"]["id"]["column_name"],
                        "value" => [
                            "identical" => $id
                        ]
                    ]
                ],
                "update_columns_values" => \array_map(function ($value) {return $value;}, \array_values($data)),
            ]
        ]);

        $response = new ApiResponse(ApiObject::USER, ApiStatus::OK);

        return $response->__serialize();
    }

    public static function Delete (array $data) : array
    {
        foreach (DatabaseInfo::TABLES as $table)
        {
            if (DatabaseInfo::hasColumn($table["table_name"], "user_id"))
            {
                DatabaseOperations::delete([
                    "table" => $table["table_name"],
                    "filters" => [
                        "where" => [
                            [
                                "field" => $table["columns"]["id"]["column_name"],
                                "value" => [
                                    "identical" => UserSession::getUser()[DatabaseInfo::USERS_TABLE["columns"]["id"]["column_name"]]
                                ]
                            ]
                        ]
                    ],
                ]);
            }
        }

        $response = new ApiResponse(ApiObject::USER, ApiStatus::OK);

        return $response->__serialize();
    }

    public static function LogOut () : void
    {
        UserSession::Delete();
        Cookie::delete(Config::USER_SESSION_COOKIE_NAME);
    }

    public static function ChangePassword (array $data) : array
    {
        if (!\array_key_exists("current_password", $data) || !\array_key_exists("new_password", $data)) Api::SetStatus(ApiStatus::BAD_REQUEST);

        $current_password = $data["current_password"];
        $new_password = $data["new_password"];

        $status = ApiStatus::OK;

        $errors = [];

        $result = self::Authenticate([
            "username_email" => UserSession::getUser()[DatabaseInfo::USERS_TABLE["columns"]["username"]["column_name"]],
            "password" => $current_password
        ]);

        $validation = Validate::Password($new_password);

        if ($result["success"] && $validation["valid"])
        {
            self::Update([
                "id" => UserSession::RetrieveUserId(),
                DatabaseInfo::USERS_TABLE["columns"]["password"]["column_name"] => Crypto::HashPassword($new_password)
            ]);
        }
        else
        {
            if (!$result["success"]) $errors["current_password"] = $result["errors"]["password"];
            
            if (!$validation["valid"])
            {
                $errors["new_password"] = [
                    "error" => $error = $validation["error"],
                    "message" => Translation::Retrieve([
                        "id" => "api->messages->user->password->$error"
                    ])["data"]["translation"]
                ];
            }

            $status = ApiStatus::FORBIDDEN;
        }

        $response = new ApiResponse(ApiObject::USER, $status, [], $errors);

        return $response->__serialize();
    }

    public static function ChangeUsername (array $data) : array
    {
        if (!\array_key_exists("username", $data)) Api::SetStatus(ApiStatus::BAD_REQUEST);

        $username = $data["username"];

        $status = ApiStatus::OK;

        $errors = [];

        if ($username !== UserSession::getUser()[DatabaseInfo::USERS_TABLE["columns"]["username"]["column_name"]])
        {
            $validation = Validate::Username($username);

            if ($validation["valid"])
            {
                self::Update([
                    "id" => UserSession::RetrieveUserId(),
                    DatabaseInfo::USERS_TABLE["columns"]["username"]["column_name"] => $username
                ]);
            }
            else
            {
                $errors["username"] = [
                    "error" => $error = $validation["error"],
                    "message" => Translation::Retrieve([
                        "id" => "api->messages->user->username->$error"
                    ])["data"]["translation"]
                ];

                $status = ApiStatus::FORBIDDEN;
            }
        }

        $response = new ApiResponse(ApiObject::USER, $status, [], $errors);

        return $response->__serialize();
    }

    private static function Register (string $id, string $username, string $email, string $hashed_password) : void
    {
        DatabaseOperations::insert([
            "table" => DatabaseInfo::USERS_TABLE['table_name'],
            "columns" => [
                DatabaseInfo::USERS_TABLE['columns']['id']["column_name"],
                DatabaseInfo::USERS_TABLE['columns']['username']["column_name"],
                DatabaseInfo::USERS_TABLE['columns']['email']["column_name"],
                DatabaseInfo::USERS_TABLE['columns']['password']["column_name"],
                DatabaseInfo::USERS_TABLE['columns']['created']["column_name"],
                DatabaseInfo::USERS_TABLE['columns']['activated']["column_name"],
                DatabaseInfo::USERS_TABLE['columns']['preferred_language']["column_name"],
            ],
            "values" => [
                $id,
                $username,
                $email,
                $hashed_password,
                \time(),
                DatabaseInfo::USERS_TABLE['columns']['activated']['default_value'],
                Translation::getCurrentLanguage(),
            ]
        ]);
    }
}