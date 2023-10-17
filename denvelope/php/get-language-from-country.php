<?php
    function getLanguageFromCountry($country){ //the english language is not necessary as it is the default one
        switch($country){
            case "IT":
                $language = "it";
                break;
            default:
                $language = "en";
                break;
        }

        return $language;
    }
?>