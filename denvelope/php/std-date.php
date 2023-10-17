<?php
    function stdDate(){
        return date("j/m/Y h:i a (T)");
    }

    function stdDateFromUnixTime($timestamp){
        return date("j/m/Y h:i a (T)", $timestamp);
    }
?>