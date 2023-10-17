<?php
    function getNumOfFoldersIn($folder){
        require("dbh.php");
        require_once("sanitize-path.php");

        $sqlQuery = "SELECT * FROM folders WHERE pathToThis LIKE ?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        $folder = sanitizePath($folder);
        $folder .= "/%";

        mysqli_stmt_bind_param($stmt, "s", $folder);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_store_result($stmt);
        $numOfFolders = mysqli_stmt_num_rows($stmt);

        return $numOfFolders;
    }
?>