<?php

namespace SignInAt\Config;

/**
 * @package SignInAt\Config
 */
class DatabaseInfo {
    public const USER_TABLE_NAME = "users";
    public const USER_TABLE_COLUMNS = array(
        "ID" => "userID",
        "USERNAME" => "username",
        "EMAIL" => "email",
        "PASSWORD" => "password"
    );

    public const APP_TABLE_NAME = "apps";
}