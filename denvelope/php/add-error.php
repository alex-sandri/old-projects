<?php
    function addError($errorCode){
        session_start();

        require("dbh.php");
        require_once("get-browser.php");
        require_once("get-os.php");
        require("std-date.php");

        $sqlQuery = "INSERT INTO errors (username, email, url, errorCode, userAgent, OS, browser, time, IPAddress) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo "An error occurred while processing the request";
            exit();
        }

        $url = $_SERVER['REQUEST_URI'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $errorTime = stdDate();
        $IPAddress = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : "not-available";
        $browser = getBrowser();
        $OS = getOS();

        if(isset($_SESSION['username'])){
            mysqli_stmt_bind_param($stmt, "sssssssss", $_SESSION['username'], $_SESSION['email'], $url, $errorCode, $userAgent, $OS, $browser, $errorTime, $IPAddress);
        }
        else{
            $usernameEmail = "unknown";
            mysqli_stmt_bind_param($stmt, "sssssssss", $usernameEmail, $usernameEmail, $url, $errorCode, $userAgent, $OS, $browser, $errorTime, $IPAddress);
        }
        
        mysqli_stmt_execute($stmt);
    }
?>