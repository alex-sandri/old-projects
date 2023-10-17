<?php
    if(!isset($_SESSION)){ //start session if not started
        session_start();
    }

    require_once("../php/create-cookie.php");

    createCookie("lang", "it", 60);

    $_SESSION['lang'] = "it"; //using session variable because the page would not use new language until refreshed if the lang cookie value changed

    require("../index.php");

    unset($_SESSION['lang']);
    exit();
?>