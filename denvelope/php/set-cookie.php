<?php
    function setSecureCookie($sessionID){
        setcookie("userSession", $sessionID, time() + 86400 * 30, "/", "denvelope.com", true, true);
    }
?>