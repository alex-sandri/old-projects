<?php
    function addLog($logType){
        require("dbh.php");
        require_once("std-date.php");

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo "An error occurred while processing the request";
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if($user['optOutLogCollection'] == 0){
            $sqlQuery = "INSERT INTO logs (username, email, logType, logTime) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                echo "An error occurred while processing the request";
                exit();
            }

            $logTime = stdDate();

            mysqli_stmt_bind_param($stmt, "ssss", $_SESSION['username'], $_SESSION['email'], $logType, $logTime);
            mysqli_stmt_execute($stmt);
        }
    }
?>