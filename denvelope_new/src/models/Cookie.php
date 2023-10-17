<?php

namespace Denvelope\Models;

require(\dirname(__DIR__) . "/autoload.php");

use Denvelope\Config\Config;
use Denvelope\Database\DatabaseInfo;
use Denvelope\Database\DatabaseOperations;

/**
 * @package Denvelope\Models
 */
class Cookie
{
    public const DURATION_LONG = 60;

    /**
     * @var string|null $name
     * @var mixed $value
     * @var int $months The number of months before the cookie expires
     * @var array $options
     */
    private $name = null;
    private $value = null;
    private $months = 1;
    private $options = array();

    public function __construct(string $name, string $value, int $months = 1){
        $this->name = $name;
        $this->value = $value;
        $this->months = $months;

        $this->options = array(
            "expires" => $this->months !== 0 // If 0 the cookie will expire when the browser is closed
                ? \time() + 86400 * 30 * $this->months
                : 0
            ,
            "path" => "/",
            "domain" => Config::isProduction() ? Config::SITE_URL : "",
            "secure" => Config::isProduction(),
            "httponly" => Config::isProduction(),
            "samesite" => "Lax",
        );

        $this->create();
    }

    private function create()
    {
        \setcookie($this->name, $this->value, $this->options);
        $_COOKIE[$this->name] = $this->value;
    }

    public static function delete(string $name)
    {
        unset($_COOKIE[$name]);

        $cookie = new Cookie(
            $name,
            "null",
            -1
        );
    }

    public static function exists(string $name){
        return isset($_COOKIE[$name]);
    }

    public function getValue(){
        return $this->value;
    }

    public static function get(string $name){
        return isset($_COOKIE[$name])
            ? $_COOKIE[$name]
            : null
        ;
    }
}