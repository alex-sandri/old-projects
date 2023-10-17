<?php
    function checkFolderPath($path){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        $folders = explode("/", $path);

        if($folders[0] == ".." && $folders[1] == "u" && $folders[2] == $user['userID']){
            return true;
        }
        else{
            return false;
        }
    }
?>