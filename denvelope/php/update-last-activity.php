<?php
    function updateLastActivity($sessionID){
        require("dbh.php");
        require_once("std-date.php");
        require_once("get-location.php");

        $sqlQuery = "UPDATE sessions SET lastActivity=?, unixTime=?, location=? WHERE sessionID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        $lastActivity = stdDate();
        $unixTime = time();

        if($isProduction){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $location = getLocation($ip);
        $location = $location['geo']['city'].", ".$location['geo']['country-name'];

        mysqli_stmt_bind_param($stmt, "ssss", $lastActivity, $unixTime, $location, $sessionID);
        mysqli_stmt_execute($stmt);
    }
?>