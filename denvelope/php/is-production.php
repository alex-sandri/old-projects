<?php
    if($_SERVER['REMOTE_ADDR'] == "::1" || $_SERVER['REMOTE_ADDR'] == "127.0.0.1"){
        $isProduction = false;
    }
    else{
        $isProduction = true;
    }
?>