<?php
    function updateLastModifiedParentFolders($path){
        require("dbh.php");
        require_once("std-date.php");

        $folders = explode("/", $path);
        $numOfFolders = count($folders);

        for($i = 3; $i < $numOfFolders; $i++){
            $folder = "";
            for($j = 0; $j <= $i; $j++){
                if($j > 0){
                    $folder .= "/".$folders[$j];
                }
                else{
                    $folder .= $folders[$j];
                }
            }
            
            $sqlQuery = "UPDATE folders SET lastModified=?, lastModifiedUnixTime=?  WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                echo 'An error occurred while processing the request';
                exit();
            }

            $lastModified = stdDate();
            $lastModifiedUnixTime = time();

            mysqli_stmt_bind_param($stmt, "sssss", $lastModified, $lastModifiedUnixTime, $_SESSION['username'], $_SESSION['email'], $folder);
            mysqli_stmt_execute($stmt);
        }
    }
?>