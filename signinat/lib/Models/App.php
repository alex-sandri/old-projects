<?php

namespace SignInAt\Models;

require(\dirname(__DIR__, 1) . "/autoload.php");

use SignInAt\Config\Config;
use SignInAt\Util\Utilities;

/**
 * @package SignInAt\Models
 */
class App {
    /**
     * @var string $id The app's unique ID
     * @var string $name The app's name
     * @var array $requires Contains all the fields that an app requires
     * @var string $app_url The app's URL
     * @var string $webhook_url The URL to which send data for this app
     * @var string $api_key The app's API key
     */
    private static $id;
    private static $name;
    private static $requires;
    private static $app_url;
    private static $webhook_url;
    private static $APIKey;

    /**
     * @param string $name The app's name
     * @param array $requires Contains all the fields that an app requires
     * @param string $webhook_url The URL to which send data for this app
     */
    public static function create(string $name, array $requires, string $app_url, string $webhook_url){
        self::$name = $name;
        self::$requires = $requires;
        self::$app_url = $app_url;
        self::$webhook_url = $webhook_url;

        self::setAppID();

        self::setAPIKey();
    }

    /**
     * Sets the app ID
     */
    private static function setAppID(){
        do{
            self::$id = Utilities::base62RandomID(Config::USER_ID_LENGTH);
        }
        while(!Utilities::uniqueID(DatabaseInfo::APP_TABLE_NAME, DatabaseInfo::APP_TABLE_COLUMNS["ID"], self::$id));
    }

    /**
     * Sets the API key
     */
    private static function setAPIKey(){
        self::$APIKey = Utilities::base62RandomID(Config::API_KEY_LENGTH);
    }
}