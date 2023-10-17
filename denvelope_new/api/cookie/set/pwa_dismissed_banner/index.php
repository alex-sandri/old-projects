<?php

header("Content-Type: application/json");

require(dirname(__FILE__, 5) . "/src/autoload.php");

use Denvelope\Config\Config;
use Denvelope\Models\CSRFToken;
use Denvelope\Models\Cookie;

if(
    isset($_POST['csrf-token'])
){
    $csrf_token = $_POST['csrf-token'];

    if(!CSRFToken::verify($csrf_token)){
        http_response_code(403);
        exit();
    }

    $pwa_dismissed_banner_cookie = new Cookie(
        Config::PWA_DISMISSED_BANNER_COOKIE_NAME,
        "true",
        Cookie::DURATION_LONG
    );

    exit();
}
else{
    http_response_code(403);
    exit();
}