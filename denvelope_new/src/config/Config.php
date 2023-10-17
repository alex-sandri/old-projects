<?php

namespace Denvelope\Config;

require(\dirname(__DIR__) . "/autoload.php");

use Denvelope\Database\DatabaseInfo;

use Denvelope\Models\File;

class Config
{
    public const SITE_NAME = "Denvelope";
    public const SITE_URL = "https://denvelope.com";

    public const USER_SESSION_COOKIE_NAME = "user_session_id";
    public const IS_LOGGED_IN_COOKIE_NAME = "is_logged_in";
    public const COOKIE_CONSENT_COOKIE_NAME = "cookie_consent";
    public const SESSION_ID_COOKIE_NAME = "session_id";
    public const PWA_DISMISSED_BANNER_COOKIE_NAME = "pwa_dismissed_banner";

    public const USER_ID_LENGTH = 25;
    public const USERNAME_MIN_LENGTH = 4;
    public const USERNAME_MAX_LENGTH = 15;
    public const USERNAME_REGEX = "/^[\w]{" . self::USERNAME_MIN_LENGTH . "," . self::USERNAME_MAX_LENGTH . "}$/";
    public const EMAIL_MAX_LENGTH = 255;
    public const PASSWORD_MIN_LENGTH = 6; // Firebase password requirement

    public const CSRF_TOKEN_LENGTH = 64;

    public const USER_SESSION_ID_LENGTH = 128;

    public const SESSION_ID_LENGTH = 64;

    public const SESSION_DURATION = 60 * 60 * 24; // Expires after one day

    public const FILE_ID_LENGTH = 25;

    public const FOLDER_ID_LENGTH = 25;

    // Password reset request expires after 10 minutes (600 seconds)
    public const PASSWORD_RESET_TOKEN_DURATION = 600;
    public const PASSWORD_RESET_TOKEN_LENGTH = 64;

    public const APIS = [
        "aws" => [
            "region" => "us-east-1",
            "bucket" => "elasticbeanstalk-us-east-1-298288330487",
            "access_key_id" => "AKIAIPLRRRNREX2TXOVQ",
            "secret_access_key" => "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",
        ],
        "smartip" => [
            "api_key" => "ec4fdc55-8aa6-4266-909d-3784691265ce",
        ],
    ];

    public const CURRENCIES = [
        "USD" => [
            "id" => "USD",
            "symbol" => "$",
        ],
        "EUR" => [
            "id" => "EUR",
            "symbol" => "€",
        ],
    ];

    public const LANGUAGES = [
        "en-US" => [
            "decimal_separator" => ".",
            "thousands_separator" => ",",
        ],
        "it-IT" => [
            "decimal_separator" => ",",
            "thousands_separator" => ".",
        ],
    ];

    public const KB = 1000;
    public const MB = 1000 * self::KB;
    public const GB = 1000 * self::MB;
    public const TB = 1000 * self::GB;

    public const PLANS = [
        "free" => [
            "name" => "Free",
            "price" => [
                self::CURRENCIES['EUR']['id'] => "000",
                self::CURRENCIES['USD']['id'] => "000",
            ],
            "storage" => 1 * self::GB,
            "features" => [
                "storage" => 1 * self::GB,
            ],
        ],
        "premium" => [
            "name" => "Premium",
            "price" => [ // Per GB
                self::CURRENCIES['EUR']['id'] => "030",
                self::CURRENCIES['USD']['id'] => "030",
            ],
            "features" => [
                
            ],
        ],
    ];

    public static function isProduction () : bool
    {
        return !($_SERVER['REMOTE_ADDR'] == "::1" || $_SERVER['REMOTE_ADDR'] == "127.0.0.1" || \strpos($_SERVER['REMOTE_ADDR'], "192.168.1.") > -1);
    }

    public static function getApiEndpoint () : string
    {
        return (
            self::isProduction()
            ? "https://api." . self::SITE_URL . "/"
            : \substr($_SERVER['REQUEST_URI'], 0, \strpos($_SERVER['REQUEST_URI'], "/", 1)) . "/api/"
        );
    }

    public static function getAppRoot () : string
    {
        return self::isProduction()
            ? self::SITE_URL
            : \substr(self::getApiEndpoint(), 0, \strpos(self::getApiEndpoint(), "/api/")) . "/public/"
        ;
    }

    public static function getFolderId () : string
    {
        $folder_id = "root";

        if (isset($_GET['folder'])) $folder_id = $_GET['folder'];
        if (isset($_GET['file'])) $folder_id = File::Retrieve(["id" => $_GET['file']])[DatabaseInfo::FILES_TABLE['columns']['parent_folder_id']["column_name"]];

        return $folder_id;
    }

    public static function getDefaultHTMLTags () : string
    {
        return "
            <meta charset=\"UTF-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
            <meta name=\"theme-color\" content=\"#160c28\">
            <meta name=\"msapplication-navbutton-color\" content=\"#160c28\">
            <meta name=\"apple-mobile-web-app-capable\" content=\"yes\">
            <meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black-translucent\">
            " .
            (
                /* PWA MANIFEST */
                self::isProduction() 
                    ? "<link rel=\"manifest\" href=\"manifest.json\">"
                    : "<link rel=\"manifest\" href=\"dev-manifest.json\">"
            )
            . "<link rel=\"apple-touch-icon\" href=\"assets/img/icons/app/icon-192.png\">
            <link rel=\"shortcut icon\" href=\"assets/img/logo/icon/icon.ico\" type=\"image/x-icon\">
            <link rel=\"stylesheet\" href=\"assets/css/min/main.min.css?v=" . \filemtime(\dirname(__FILE__, 3) . "/public/assets/css/min/main.min.css") . "\">
            <link rel=\"stylesheet\" href=\"https://use.fontawesome.com/releases/v5.11.2/css/all.css\">
        ";
    }
}