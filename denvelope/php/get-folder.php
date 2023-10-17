<?php
    function getFolder($folderID){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM folders WHERE folderID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $folderID);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $folder = mysqli_fetch_assoc($result);

        return $folder;
    }
?>