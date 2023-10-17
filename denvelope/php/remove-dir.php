<?php
    function removeDir($path) {
        require("dbh.php");
        require_once("add-log.php");
        require("delete-folder-from-s3.php");
        require("sanitize-path.php");

        $path = sanitizePath($path);

        $path .= "/%"; //the '/' is used to restrict to folders and files actually in this folder

        $sqlQuery = "DELETE FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis LIKE ?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $path);
        mysqli_stmt_execute($stmt);

        $rowsAffected = mysqli_stmt_affected_rows($stmt);
        $rowsAffected++;

        addLog("FOLDER_DELETED_FROM_PARENT_FOLDER_DELETE*".$rowsAffected);

        $sqlQuery = "SELECT pathToThis FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis LIKE ?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $path);
        mysqli_stmt_execute($stmt);

        $objects = mysqli_stmt_get_result($stmt);
        
        $sqlQuery = "DELETE FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis LIKE ?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $path);
        mysqli_stmt_execute($stmt);

        $rowsAffected = mysqli_stmt_affected_rows($stmt);

        addLog("FILES_DELETED_FROM_PARENT_FOLDER_DELETE*".$rowsAffected);

        if($rowsAffected > 0){
            deleteFolderFromS3($objects);
        }
    }
?>