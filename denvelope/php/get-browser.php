<?php
    function getBrowser(){
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        if(strpos($userAgent, "Firefox")){
            $browser = "firefox";
        }
        else if(strpos($userAgent, "Opera") || strpos($userAgent, "OPR") || strpos($userAgent, "OPT")){
            $browser = "opera";
        }
        else if(strpos($userAgent, "Trident")){
            $browser = "internet-explorer";
        }
        else if(strpos($userAgent, "Edge") || strpos($userAgent, "EdgA")){
            $browser = "edge";
        }
        else if(strpos($userAgent, "Chrome")){
            $browser = "chrome";
        }
        else if(strpos($userAgent, "Safari")){
            $browser = "safari";
        }
        else{
            $browser = "unknown";
        }

        return $browser;
    }
?>