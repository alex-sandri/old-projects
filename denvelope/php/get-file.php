<?php
    function getFile($fileID){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM files WHERE fileID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $fileID);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $file = mysqli_fetch_assoc($result);

        return $file;
    }
?>