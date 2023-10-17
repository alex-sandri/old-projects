<?php
    function getSupportCases(){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM support_cases WHERE status='open'";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }
        
        mysqli_stmt_execute($stmt);

        $supportCases = mysqli_stmt_get_result($stmt);

        return $supportCases;
    }

    function getSupportCasesByUser(){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM support_cases WHERE (username=? OR email=?) AND status='open'";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }
        
        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $supportCases = mysqli_stmt_get_result($stmt);

        return $supportCases;
    }
?>