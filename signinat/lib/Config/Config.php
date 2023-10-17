<?php

namespace SignInAt\Config;

/**
 * @package SignInAt\Config
 */
class Config {
    /**
     * @var int The length of the user ID
     * @var string The regex to validate the username with
     */
    public const USER_ID_LENGTH = 25;

    public const USERNAME_MIN_LENGTH = 4;
    public const USERNAME_MAX_LENGTH = 15;
    public const USERNAME_REGEX = "/^[\w]{" . self::USERNAME_MIN_LENGTH . "," . self::USERNAME_MAX_LENGTH . "}$/";

    public const EMAIL_MAX_LENGTH = 255;

    public const PASSWORD_MIN_LENGTH = 12;
    
    /**
     * @var int APP_ID_LENGTH The length of the app ID
     * @var int API_KEY_LENGTH The length og the API key
     */
    public const APP_ID_LENGTH = 25;
    public const API_KEY_LENGTH = 64;

    public const DEFAULT_INPUT_VALIDATION_ERROR_MSG = "no_errors";
    public const INPUT_VALIDATION_ERROR_MSG = array(
        "EMPTY" => "empty",
        "SHORT" => "short",
        "LONG" => "long",
        "INVALID" => "invalid",
        "TAKEN" => "taken",
    );

    public const API_ACTIONS = array(
        "USER_CREATE" => "USER_CREATE",
    );

    public const FONTAWESOME_URL = "https://use.fontawesome.com/releases/v5.11.2/css/all.css";
}