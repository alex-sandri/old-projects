<?php
    function authyAPI(){
        require("../vendor/autoload.php");
        require("../php/global-vars.php");

        return new Authy\AuthyApi($authyAPIKey);
    }
?>