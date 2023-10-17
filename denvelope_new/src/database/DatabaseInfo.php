<?php

namespace Denvelope\Database;

require(\dirname(__FILE__, 2) . "/autoload.php");

use Denvelope\Utils\Translation;

/**
 * @package Denvelope\Database
 */
class DatabaseInfo {
    public const DATA_TYPES = array(
        "bool" => [
            "values" => [
                "true" => "TRUE",
                "false" => "FALSE",
            ]
        ]
    );

    public const TABLES = array(
        "files" => [
            "table_name" => "files",
            "columns" => [
                "id" => [
                    "column_name" => "id",
                    "type" => "string",
                ],
                "user_id" => [
                    "column_name" => "user_id",
                    "type" => "string",
                ],
                "name" => [
                    "column_name" => "name",
                    "type" => "string",
                ],
                "parent_folder_id" => [
                    "column_name" => "parent_folder_id",
                    "type" => "string",
                ],
                "id_path" => [
                    "column_name" => "id_path",
                    "type" => "string",
                ],
            ]
        ],
        "folders" => [
            "table_name" => "folders",
            "columns" => [
                "id" => [
                    "column_name" => "id",
                    "type" => "string",
                ],
                "user_id" => [
                    "column_name" => "user_id",
                    "type" => "string",
                ],
                "name" => [
                    "column_name" => "name",
                    "type" => "string",
                ],
                "created" => [
                    "column_name" => "created",
                    "type" => "string",
                ],
                "parent_folder_id" => [
                    "column_name" => "parent_folder_id",
                    "type" => "string",
                ],
                "id_path" => [
                    "column_name" => "id_path",
                    "type" => "string",
                ],
            ]
        ],
        "password_resets" => [
            "table_name" => "password_resets",
            "columns" => [
                "token" => [
                    "column_name" => "token",
                    "type" => "string",
                ],
                "user_id" => [
                    "column_name" => "user_id",
                    "type" => "string",
                ],
                "expires" => [
                    "column_name" => "expires",
                    "type" => "string",
                ],
            ]
        ],
        "sessions" => [
            "table_name" => "sessions",
            "columns" => [
                "token" => [
                    "column_name" => "token",
                    "type" => "string",
                ],
                "data" => [
                    "column_name" => "data",
                    "type" => "string",
                ],
                "expires" => [
                    "column_name" => "expires",
                    "type" => "string",
                ]
            ]
        ],
        "users" => [
            "table_name" => "users",
            "columns" => [
                "id" => [
                    "column_name" => "user_id",
                    "type" => "string",
                ],
                "username" => [
                    "column_name" => "username",
                    "type" => "string",
                ],
                "email" => [
                    "column_name" => "email",
                    "type" => "string",
                ],
                "password" => [
                    "column_name" => "password",
                    "type" => "string",
                ],
                "created" => [
                    "column_name" => "created",
                    "type" => "string",
                ],
                "activated" => [
                    "column_name" => "activated",
                    "type" => "bool",
                    "default_value" => self::DATA_TYPES['bool']['values']['false'],
                ],
                "preferred_language" => [
                    "column_name" => "preferred_language",
                    "type" => "string",
                ],
            ]
        ],
        "user_sessions" => [
            "table_name" => "user_sessions",
            "columns" => [
                "token" => [
                    "column_name" => "token",
                    "type" => "string",
                ],
                "user_id" => [
                    "column_name" => "user_id",
                    "type" => "string",
                ],
            ]
        ],
    );

    public const FILES_TABLE = self::TABLES['files'];
    public const FOLDERS_TABLE = self::TABLES['folders'];
    public const PASSWORD_RESETS_TABLE = self::TABLES['password_resets'];
    public const SESSIONS_TABLE = self::TABLES['sessions'];
    public const USERS_TABLE = self::TABLES['users'];
    public const USER_SESSIONS_TABLE = self::TABLES['user_sessions'];

    public static function hasColumn (string $table, string $column) : bool
    {
        return \array_key_exists($column, self::TABLES[$table]['columns']);
    }
}