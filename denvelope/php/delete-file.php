<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        
        session_start();

        require("dbh.php");
        require("add-log.php");
        require("update-parent-folders-size.php");
        require("delete-from-s3.php");
        require("update-last-modified-parent-folders.php");
        require("update-last-activity.php");
        require("get-num-of-files-in-folder.php");
        require("get-num-of-folders-in-folder.php");

        $fileID = $_POST['fileID'];

        $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND fileID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['sqlError'] = true;

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $fileID);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $file = mysqli_fetch_assoc($result);

        $sqlQuery = "DELETE FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND fileID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['sqlError'] = true;

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $fileID);
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

            $usedStorageInBytes = $user['usedStorageInBytes'] - $file['sizeInBytes'];
            $usedStorage = betterSize($usedStorageInBytes);

            mysqli_stmt_bind_param($stmt, "ssss", $usedStorage, $usedStorageInBytes, $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);

            addLog("FILE_DELETED");

            deleteFileFromS3($file['pathToThis']);

            updateAllParentFoldersSize($file['folder'], -$file['sizeInBytes']);

            updateLastActivity($_COOKIE['userSession']);

            updateLastModifiedParentFolders($file['folder']);

            $_SESSION['fileDeleteSuccess'] = true;
            $_SESSION['usedStorage'] = $usedStorage;
            $_SESSION['emptyFolder'] = false;

            if(getNumOfFilesIn($file['folder']) == 0 && getNumOfFoldersIn($file['folder']) == 0){
                $_SESSION['emptyFolder'] = true;
            }

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }
    }
    else{
        header("Location: ../");
        exit();
    }

    function betterSize($fileSize){

        for($i = 0; $fileSize >= 1024; $i++){
            $fileSize /= 1024;
        }

        $fileSize = round($fileSize);

        switch ($i) {
            case 0:
                $betterSize = $fileSize."B";
                break;
            case 1:
                $betterSize = $fileSize."KB";
                break;
            case 2:
                $betterSize = $fileSize."MB";
                break;
            case 3:
                $betterSize = $fileSize."GB";
                break;
            case 4:
                $betterSize = $fileSize."TB";
                break;
            case 5:
                $betterSize = $fileSize."PB";
                break;
            case 6:
                $betterSize = $fileSize."EB"; //exabyte
                break;
            case 7:
                $betterSize = $fileSize."ZB"; //zettabyte
                break;
            case 8:
                $betterSize = $fileSize."YB"; //yottabyte
                break;
        }

        return $betterSize;
    }

    function fileSizeUnitPriority($unit)
    {
        switch ($unit) {
            case "B ":
                $priority = 0;
                break;
            case "KB":
                $priority = 1;
                break;
            case "MB":
                $priority = 2;
                break;
            case "GB":
                $priority = 3;
                break;
            case "TB":
                $priority = 4;
                break;
            case "PB":
                $priority = 5;
                break;
            case "EB":
                $priority = 6;
                break;
            case "ZB":
                $priority = 7;
                break;
            case "YB":
                $priority = 8;
                break;
        }

        return $priority;
    }
?>