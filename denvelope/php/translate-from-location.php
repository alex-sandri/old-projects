<?php
    require_once("get-location.php");
    require_once("create-cookie.php");
    require_once("get-language-from-country.php");
    require("is-production.php");

    if($isProduction){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

        $result = getLocation($ip);

        //$language = $result['location']['languages'][0]['code']; ipstack API

        $country = $result['geo']['country-iso-code'];

        $language = getLanguageFromCountry($country);
    }
    else{
        $language = "it";
    }

    if(($language == "en" || $language == "it") && !isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
        createCookie("lang", $language, 60);

        $_SESSION['langFromIP'] = $language;
    }
?>