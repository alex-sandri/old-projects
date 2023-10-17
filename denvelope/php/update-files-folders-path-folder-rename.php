<?php
    function updateFilesFoldersPathFolderRename($objectKeys, $foldersToUpdate, $oldFolderPath, $newFolderPath){
        require("dbh.php");

        mysqli_autocommit($conn, FALSE);

        foreach ($objectKeys as $objectKey) {
            $sqlQuery = "UPDATE files SET folder=?, pathToThis=? WHERE pathToThis=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                exit();
            }

            $newFolder = $newFolderPath.substr($objectKey['folder'], strlen($oldFolderPath));
            $newPathToThis = $newFolderPath.substr($objectKey['pathToThis'], strlen($oldFolderPath));

            mysqli_stmt_bind_param($stmt, "sss", $newFolder, $newPathToThis, $objectKey['pathToThis']);
            mysqli_stmt_execute($stmt);
        }

        foreach ($foldersToUpdate as $folder) {
            $sqlQuery = "UPDATE folders SET folder=?, pathToThis=? WHERE pathToThis=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                exit();
            }

            $newFolder = $newFolderPath.substr($folder['folder'], strlen($oldFolderPath));
            $newPathToThis = $newFolderPath.substr($folder['pathToThis'], strlen($oldFolderPath));

            mysqli_stmt_bind_param($stmt, "sss", $newFolder, $newPathToThis, $folder['pathToThis']);
            mysqli_stmt_execute($stmt);
        }

        mysqli_commit($conn);
        mysqli_autocommit($conn, TRUE);

        $path = $newFolderPath;
        $path = sanitizePath($path);
        $path .= "/%";

        $sqlQuery = "SELECT pathToThis FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis LIKE ?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $path);
        mysqli_stmt_execute($stmt);

        $objects = mysqli_stmt_get_result($stmt);

        return $objects;
    }
?>