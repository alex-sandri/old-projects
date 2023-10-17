<?php
    function getTables(){
        require("../php/dbh.php");

        $sqlQuery = "SHOW TABLES";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_execute($stmt);

        $tables = mysqli_stmt_get_result($stmt);

        return $tables;
    }
?>