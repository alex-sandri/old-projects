<?php
    function getLocation($ip){
        $_SESSION['requiredFromFileIgnoreCookie'] = true;

        require("global-vars.php");

        /* ipstack API
        $access_key = $ipstackAccessKey;

        $ch = curl_init('http://api.ipstack.com/'.$ip.'?access_key='.$access_key.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($json, true);
        */

        if($isProduction){
            $api_key = $smartipAPIKey;

            $ch = curl_init('https://api.smartip.io/'.$ip.'?api_key='.$api_key.'');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $json = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($json, true);
        }
        else{
            $result = array( //fake the location on localhost, because if not the result will have been "," when needing the location
                "country" => [
                    "country-name" => "Somewhere",
                ],
                "location" => [
                    "city" => "Somewhere",
                ],
            );
        }

        return $result;
    }
?>