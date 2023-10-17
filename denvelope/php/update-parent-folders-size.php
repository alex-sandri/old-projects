<?php
    function updateAllParentFoldersSize($path, $bytesToAdd){
        
        require("dbh.php");

        $folders = explode("/", $path);
        $numOfFolders = count($folders);

        for($i = 3; $i < $numOfFolders; $i++){
            $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['fileUploadError'] = "sqlError";

                header("Location: ../account");
                exit();
            }

            $pathToThis = "";
            for($j = 0; $j <= $i; $j++){
                $pathToThis .= $folders[$j]."/";
            }

            $pathToThis = substr($pathToThis, 0, strlen($pathToThis) - 1);

            mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $pathToThis);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $folder = mysqli_fetch_assoc($result);

            $sqlQuery = "UPDATE folders SET size=?, sizeInBytes=?  WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['fileUploadError'] = "sqlError";

                header("Location: ../account");
                exit();
            }

            $sizeInBytes = $folder['sizeInBytes'] + $bytesToAdd;
            $size = betterSize($sizeInBytes);

            mysqli_stmt_bind_param($stmt, "sssss", $size, $sizeInBytes, $_SESSION['username'], $_SESSION['email'], $pathToThis);
            mysqli_stmt_execute($stmt);
        }
    }
?>