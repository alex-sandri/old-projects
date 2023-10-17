<?php
    function getColumns($table){
        require("../php/dbh.php");
        require("../php/db-info.php");

        if(!in_array($table, $tables)){
            echo 'An error occurred while processing the request';
            exit();
        }

        $sqlQuery = "SHOW COLUMNS IN $table";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_execute($stmt);

        $columns = mysqli_stmt_get_result($stmt);

        return $columns;
    }
?>