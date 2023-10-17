<?php
    if(!isset($_SESSION)){
        session_start();
    }

    require_once("../php/create-cookie.php");

    createCookie("lang", "en", 60);

    $_SESSION['lang'] = "en";

    require("../index.php");

    unset($_SESSION['lang']);
    exit();
?>