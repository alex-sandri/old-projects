<?php
    function createCookie($cookie, $value, $months = 1){
        require("is-production.php");

        //Add this when an update to PHP 7.3 is available
        $options = array(
            "expires" => time() + 86400 * 30 * $months,
            "path" => "/",
            "domain" => "denvelope.com",
            "secure" => true,
            "httponly" => true,
            "samesite" => "Lax",
        );

        if($isProduction){
            setcookie($cookie, $value, time() + 86400 * 30 * $months, "/", "denvelope.com", true, true);
        }
        else{
            setcookie($cookie, $value, time() + 86400 * 30 * $months, "/", "");
        }
    }
?>