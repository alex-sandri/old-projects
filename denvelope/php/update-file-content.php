<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        session_start();

        require("dbh.php");
        require("better-size.php");
        require("update-parent-folders-size.php");
        require("std-date.php");
        require("upload-to-s3.php");
        require("convert-to-bytes.php");
        require("update-last-modified-parent-folders.php");

        $content = $_POST['content'];
        $id = $_POST['id'];

        $newFileSize = strlen($content);
        $betterSize = betterSize($newFileSize);

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['fileUpdateError'] = "sqlError";

            header("Location: ../account");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if($newFileSize > convertToBytes($user['maxStorage']) - $user['usedStorageInBytes']){
            $_SESSION['fileSizeError'] = "exceedMaxStorage";

            header("Location: ../account");
            exit();
        }

        $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND fileID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['fileUpdateError'] = "sqlError";

            header("Location: ../account");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $file = mysqli_fetch_assoc($result);

        $sqlQuery = "UPDATE files SET lastModified=?, unixTimeLastModified=?, size=?, sizeInBytes=? WHERE (usernameAuthor=? OR emailAuthor=?) AND fileID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['fileUpdateError'] = "sqlError";

            header("Location: ../account");
            exit();
        }

        $lastModified = stdDate();
        $unixTimeLastModified = time();

        mysqli_stmt_bind_param($stmt, "sssssss", $lastModified, $unixTimeLastModified, $betterSize, $newFileSize, $_SESSION['username'], $_SESSION['email'], $id);
        mysqli_stmt_execute($stmt);

        $sqlQuery = "UPDATE users SET usedStorage=?, usedStorageInBytes=?, filesEditedAfterUpload=? WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['fileUpdateError'] = "sqlError";

            header("Location: ../account");
            exit();
        }

        $usedStorageInBytes = $user['usedStorageInBytes'] - $file['sizeInBytes'] + $newFileSize;
        $usedStorage = betterSize($usedStorageInBytes);
        $filesEditedAfterUpload = $user['filesEditedAfterUpload'] + 1;

        mysqli_stmt_bind_param($stmt, "sssss", $usedStorage, $usedStorageInBytes, $filesEditedAfterUpload, $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        updateAllParentFoldersSize($file['folder'], $newFileSize - $file['sizeInBytes']);

        $filePath = "../user-content-tmp/".$file['fileID'];

        $writeFile = fopen($filePath, "w") or die ("Unable to open file!");
        fwrite($writeFile, $content);
        fclose($writeFile);

        uploadFileToS3($filePath, $file['pathToThis']);

        updateLastModifiedParentFolders($file['folder']);

        $_SESSION['fileUpdatedSuccess'] = true;
        $_SESSION['lastModifiedSavedFile'] = $lastModified;

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