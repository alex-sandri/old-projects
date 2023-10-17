<?php
    function getSupportCase($caseNumber){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM support_cases WHERE caseNumber=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }
        
        mysqli_stmt_bind_param($stmt, "s", $caseNumber);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $case = mysqli_fetch_assoc($result);

        return $case;
    }
?>