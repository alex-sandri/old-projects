<?php
    if(isset($_POST['folderID']) && isset($_POST['name']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        session_start();

        require("dbh.php");
        require("std-date.php");
        require("update-last-activity.php");
        require("get-folder.php");
        require("update-last-modified-parent-folders.php");
        require("already-exists.php");
        require("sanitize-path.php");
        require("update-files-folders-path-folder-rename.php");
        require("rename-folder-s3.php");
        require("add-log.php");

        unset($_SESSION['folderRenameError']);
        unset($_SESSION['folderRenamedCorrectly']);
        unset($_SESSION['folderRenameLastModified']);

        $name = $_POST['name'];
        $folderID = $_POST['folderID'];

        $folder = getFolder($folderID);

        $pathToThis = $folder['folder']."/$name";

        $newPathAndName = exists($pathToThis, $name, "folders");

        if($newPathAndName[0] != $pathToThis){
            $pathToThis = $newPathAndName[0];
            $name = $newPathAndName[1];
        }

        if(empty($name)){
            $_SESSION['folderRenameError'] = "emptyName";

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }

        $sqlQuery = "UPDATE folders SET name=?, lastModified=?, lastModifiedUnixTime=?, pathToThis=? WHERE folderID=? AND (usernameAuthor=? OR emailAuthor=?)";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }

        $lastModified = stdDate();
        $unixTimeLastModified = time();

        mysqli_stmt_bind_param($stmt, "sssssss", $name, $lastModified, $unixTimeLastModified, $pathToThis, $folderID, $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $path = $folder['pathToThis'];
        $path = sanitizePath($path);
        $path .= "/%";

        $sqlQuery = "SELECT folder, pathToThis FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis LIKE ?";
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

        $sqlQuery = "SELECT folder, pathToThis FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis LIKE ?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $path);
        mysqli_stmt_execute($stmt);

        $foldersToUpdatePath = mysqli_stmt_get_result($stmt);

        updateLastActivity($_COOKIE['userSession']);

        updateLastModifiedParentFolders($folder['folder']);

        $newObjectKeys = updateFilesFoldersPathFolderRename($objects, $foldersToUpdatePath, $folder['pathToThis'], $pathToThis);

        renameFolderS3($objects, $newObjectKeys);

        addLog("FOLDER_RENAMED");

        $_SESSION['folderRenamedCorrectly'] = true;
        $_SESSION['folderRenameLastModified'] = $lastModified;
        $_SESSION['folderRenameName'] = $name;

        header('Content-type: application/json');

        $JSONdata = array($_SESSION);
        echo json_encode($JSONdata);

        exit();
    }
    else{
        header("Location: ../");
        exit();
    }
?>