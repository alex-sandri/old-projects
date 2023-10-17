<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        
        session_start();

        require("dbh.php");
        require("better-size.php");
        require("remove-dir.php");
        require("add-log.php");
        require("update-last-modified-parent-folders.php");
        require("get-folder-by-path.php");
        require("update-parent-folders-size.php");
        require("get-num-of-files-in-folder.php");
        require("get-num-of-folders-in-folder.php");

        $folderID = $_POST['folderID'];

        $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['sqlError'] = true;

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $folderID);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $folder = mysqli_fetch_assoc($result);

        $folderRedirect = getFolderWithPath($folder['folder']);

        $sqlQuery = "DELETE FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['sqlError'] = true;

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $folderID);
            mysqli_stmt_execute($stmt);

            $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['sqlError'] = true;

                header('Content-type: application/json');

                $JSONdata = array($_SESSION);
                echo json_encode($JSONdata);
    
                exit();
            }

            mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            $sqlQuery = "UPDATE users SET usedStorage=?, usedStorageInBytes=? WHERE username=? OR email=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['sqlError'] = true;

                header('Content-type: application/json');

                $JSONdata = array($_SESSION);
                echo json_encode($JSONdata);
    
                exit();
            }

            $usedStorageInBytes = $user['usedStorageInBytes'] - $folder['sizeInBytes'];
            $usedStorage = betterSize($usedStorageInBytes);

            mysqli_stmt_bind_param($stmt, "ssss", $usedStorage, $usedStorageInBytes, $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);

            addLog("FOLDER_DELETED");

            removeDir($folder['pathToThis']);

            updateLastModifiedParentFolders($folder['folder']);

            updateAllParentFoldersSize($folder['folder'], -$folder['sizeInBytes']);

            $_SESSION['folderDeleteSuccess'] = true;
            $_SESSION['usedStorage'] = $usedStorage;
            $_SESSION['emptyFolder'] = false;

            if(getNumOfFilesIn($folder['folder']) == 0 && getNumOfFoldersIn($folder['folder']) == 0){
                $_SESSION['emptyFolder'] = true;
            }

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);
    
            exit();

            /* now using AJAX so this is completely useless
            if(!$folderRedirect){
                header("Location: ../account");
                exit();
            }

            header("Location: ../account?folder=".$folderRedirect['folderID']);
            exit();
            */
        }
    }
    else{
        header("Location: ../");
        exit();
    }
?>