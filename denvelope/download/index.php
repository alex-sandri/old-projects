<?php
    if(isset($_GET['sid'])){
        session_start();

        require("../php/get-file.php");
        require("../php/download-from-s3.php");

        $file = getFile($_GET['sid']);
        
        if(!$file){
            header("Location: ../");
            exit();
        }

        downloadFromS3($file['pathToThis'], $file['name'], $file['type'], $file['sizeInBytes']);
    }
    else if(isset($_GET['fid'])){
        session_start();

        require("../php/dbh.php");
        require("../php/get-folder.php");
        require("../php/create-zip-folder.php");
        require("../php/sanitize-path.php");

        $folder = getFolder($_GET['fid']);
        
        if(!$folder){
            header("Location: ../");
            exit();
        }

        $sqlQuery = "SELECT name, pathToThis FROM files WHERE pathToThis LIKE ?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        $folderPath = $folder['pathToThis'];

        $folderPath = sanitizePath($folderPath)."/%";

        mysqli_stmt_bind_param($stmt, "s", $folderPath);
        mysqli_stmt_execute($stmt);

        $objectKeys = mysqli_stmt_get_result($stmt);

        $pathAndName = createZipFolder($folder['folderID'], $folder['name'], $objectKeys, $folder['pathToThis']);

        header("Content-Type: application/zip");
        header("Content-Length: ".filesize($pathAndName['path']));
        header("Content-Disposition: attachment; filename=".$pathAndName['name']);

        readfile($pathAndName['path']);

        unlink($pathAndName['path']);
    }
    else{
        header("Location: ../");
        exit();
    }
?>