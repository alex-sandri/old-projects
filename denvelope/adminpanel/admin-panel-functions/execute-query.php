<?php
    function executeQuery($query){
        require("../php/dbh.php");

        $sqlQuery = $query;
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_execute($stmt);
    }
?>