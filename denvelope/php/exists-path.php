<?php
    function existsPath($path, $table){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM $table WHERE pathToThis=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $path);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $numOfRows = mysqli_stmt_num_rows($stmt);

        if($numOfRows > 0){
            $exists = true;
        }
        else{
            $exists = false;
        }

        return $exists;
    }
?>