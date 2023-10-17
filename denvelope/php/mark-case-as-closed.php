<?php
    function markCaseAsClosed($caseNumber){
        require("dbh.php");

        $sqlQuery = "UPDATE support_cases SET status='closed' WHERE caseNumber=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $caseNumber);
        mysqli_stmt_execute($stmt);
    }
?>