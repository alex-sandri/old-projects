<?php
    function deleteCookie($cookie){
        require_once("global-vars.php");

        if($isProduction){
            setcookie($cookie, NULL, time() - 1, "/", "denvelope.com", true, true);
        }
        else{
            setcookie($cookie, NULL, time() - 1, "/", "");
        }
    }
?>