<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require(dirname(__DIR__, 3) . "/lib/autoload.php");

use SignInAt\Models\User;

if(isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"])){
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $user = User::create(
        $username,
        $email,
        $password
    );
}