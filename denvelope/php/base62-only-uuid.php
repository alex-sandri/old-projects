<?php
    function base62UUID($strLength){
        require("dbh.php");

        $charset = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $uuid = "";

        for($i = 0; $i < $strLength; $i++){
            $uuid .= $charset[mt_rand(0, 61)];
        }

        return $uuid;
    }
?>