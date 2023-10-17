<?php
    function getOS(){
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $os = "unknown";

        if(strpos($userAgent, "Win")){
            $os = "windows";
        }
        if(strpos($userAgent, "Mac")){
            $os = "macos";
        }
        if(strpos($userAgent, "Linux")){
            $os = "linux";
        }
        if(strpos($userAgent, "iPhone")){
            $os = "ios";
        }
        if(strpos($userAgent, "Android")){
            $os = "android";
        }

        return $os;
    }
?>