<?php
    function checkLang(){
        if(isset($_COOKIE['lang'])){
            $lang = $_COOKIE['lang'];

            switch($lang){
                case "en":
                case "it":
                    $isValidLang = true;
                    break;
                default:
                    $isValidLang = false;
                    break;
            }

            return $isValidLang;
        }
        else{
            return true;
        }
    }
?>