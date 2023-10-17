<?php
    if(isset($_POST['fileID']) && isset($_POST['name']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        session_start();

        require("dbh.php");
        require("std-date.php");
        require("update-last-activity.php");
        require("get-file.php");
        require("update-last-modified-parent-folders.php");
        require("already-exists.php");
        require("rename-file-s3.php");
        require("add-log.php");

        unset($_SESSION['fileRenameError']);
        unset($_SESSION['fileRenamedCorrectly']);
        unset($_SESSION['fileRenameLastModified']);

        $name = $_POST['name'];
        $fileID = $_POST['fileID'];

        $file = getFile($fileID);

        $pathToThis = $file['folder']."/$name";

        $newPathAndName = exists($pathToThis, $name, "files", $file['fileID']);

        if($newPathAndName[0] != $pathToThis){
            $pathToThis = $newPathAndName[0];
            $name = $newPathAndName[1];
        }

        if(empty($name)){
            $_SESSION['fileRenameError'] = "emptyName";

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }

        $sqlQuery = "UPDATE files SET name=?, lastModified=?, unixTimeLastModified=?, pathToThis=? WHERE fileID=? AND (usernameAuthor=? OR emailAuthor=?)";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }

        $lastModified = stdDate();
        $unixTimeLastModified = time();

        mysqli_stmt_bind_param($stmt, "sssssss", $name, $lastModified, $unixTimeLastModified, $pathToThis, $fileID, $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        updateLastActivity($_COOKIE['userSession']);

        updateLastModifiedParentFolders($file['folder']);

        renameFileS3($file['pathToThis'], $pathToThis);

        addLog("FILE_RENAMED");

        $_SESSION['fileRenamedCorrectly'] = true;
        $_SESSION['fileRenameLastModified'] = $lastModified;
        $_SESSION['fileRenameName'] = $name;

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