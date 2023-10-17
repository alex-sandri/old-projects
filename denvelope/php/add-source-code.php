<?php
    if(isset($_POST['add-source-code-save-button-single']) || isset($_FILES['file-single'])){

        session_start();

        require("dbh.php");
        require("update-parent-folders-size.php");
        require("add-log.php");
        require("upload-to-s3.php");
        require("std-date.php");
        require("already-exists.php");
        require("get-folder-by-path.php");
        require("check-folder-path.php");
        require("update-last-modified-parent-folders.php");
        require("file-icon.php");

        $file = $_FILES['file-single'];

        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size']; //in bytes
        $fileError = $file['error'];
        $fileType = $file['type'];

        $newFileName = $_POST['source-name'];
        $path = $_POST['path-to-this'];

        if(!checkFolderPath($path)){
            header("Location: ../account");
            exit();
        }

        if($fileName != $newFileName && !empty($newFileName)){
            $fileName = $newFileName;
        }

        if($fileError !== 0){
            $_SESSION['fileUploadError'] = "uploadError";

            header("Location: ../account");
            exit();
        }

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['fileUploadError'] = "sqlError";

            header("Location: ../account");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);
            
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if($fileSize > convertToBytes($row['maxStorage']) - $row['usedStorageInBytes']){
                $_SESSION['fileSizeError'] = "exceedMaxStorage";

                header("Location: ../account");
                exit();
            }

            $userID = $row['userID'];

            $fileSizeInBytes = $fileSize;

            $fileID = base62(25, "files");
            $created = stdDate();
            $unixTimeCreated = $unixTimeLastModified = time();
            $lastModified = $created; //equal to created because just uploaded
            $language = $_POST['language'];
            $fileSize = betterSize($fileSize);
            $type = $fileType;

            $fileIcon = getFileIcon($language);

            $fileDestination = $path."/".$fileName;
            
            $newPathAndName = exists($fileDestination, $fileName, "files"); //returns a new path and name if the file already exists

            if($newPathAndName[0] != $fileDestination){
                $fileDestination = $newPathAndName[0];
                $fileName = $newPathAndName[1];
            }

            $folder = $path;
            $pathToThis = $fileDestination;

            $sqlQuery = "INSERT INTO files (usernameAuthor, emailAuthor, fileID, name, created, unixTimeCreated, lastModified, unixTimeLastModified, language, size, sizeInBytes, type, fileIcon, folder, pathToThis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['fileUploadError'] = "sqlError";

                header("Location: ../account");
                exit();
            }
            else{
                mysqli_stmt_bind_param($stmt, "sssssssssssssss", $_SESSION['username'], $_SESSION['email'], $fileID, $fileName, $created, $unixTimeCreated, $lastModified, $unixTimeLastModified, $language, $fileSize, $fileSizeInBytes, $type, $fileIcon, $folder, $pathToThis);
                mysqli_stmt_execute($stmt);
            }

            $sqlQuery = "UPDATE users SET usedStorage=?, usedStorageInBytes=?, filesUploaded=? WHERE username=? OR email=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['fileUploadError'] = "sqlError";

                header("Location: ../account");
                exit();
            }
            else{
                $usedStorageInBytes = $row['usedStorageInBytes'] + $fileSizeInBytes;
                $usedStorage = betterSize($usedStorageInBytes);
                $filesUploaded = $row['filesUploaded'] + 1;

                mysqli_stmt_bind_param($stmt, "sssss", $usedStorage, $usedStorageInBytes, $filesUploaded, $_SESSION['username'], $_SESSION['email']);
                mysqli_stmt_execute($stmt);
            }

            uploadFileToS3($fileTmpName, $fileDestination);

            updateAllParentFoldersSize($path, $fileSizeInBytes);

            updateLastModifiedParentFolders($path);

            addLog("SINGLE_FILE_UPLOAD");

            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                header("Content-Type: application/json");
    
                $JSONResponse = array(
                    "uploadSuccess" => true,
                );
    
                echo json_encode($JSONResponse);
    
                exit();
            }

            $folder = getFolderWithPath($path);

            if(!$folder){
                header("Location: ../account");
                exit();
            }

            header("Location: ../account?folder=".$folder['folderID']);
            exit();
        }
    }
    else if(isset($_POST['add-source-code-project-button']) || isset($_FILES['file-project'])){
        session_start();

        require("dbh.php");
        require("update-parent-folders-size.php");
        require("add-log.php");
        require("upload-to-s3.php");
        require("std-date.php");
        require("already-exists.php");
        require("get-folder-by-path.php");
        require("check-folder-path.php");
        require("update-last-modified-parent-folders.php");
        require("file-icon.php");

        $numOfFiles = count($_FILES['file-project']['name']);
        $pathArray = json_decode($_POST['path-array']);
        $langArray = json_decode($_POST['lang-array']);
        $path = $_POST['path-to-this'];
        $folderName = $_POST['source-name-project'];

        if(empty($folderName)){
            header("Location: ../account");
            exit();
        }

        if(!checkFolderPath($path)){
            header("Location: ../account");
            exit();
        }

        $folderSize = 0;
        foreach ($_FILES['file-project']['size'] as $size) {
            $folderSize += $size;
        }

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['folderUploadError'] = "sqlError";

            header("Location: ../account");
            exit();
        }
        
        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);
            
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if($folderSize > convertToBytes($user['maxStorage']) - $user['usedStorageInBytes']){
            $_SESSION['folderSizeError'] = "exceedMaxStorage";

            header("Location: ../account");
            exit();
        }

        $sqlQuery = "INSERT INTO folders (usernameAuthor, emailAuthor, folderID, name, created, createdUnixTime, lastModified, lastModifiedUnixTime, size, sizeInBytes, folder, pathToThis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        
        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['folderUploadError'] = "sqlError";

            header("Location: ../account");
            exit();
        }

        $folderID = base62(25, "folders");
        $created = stdDate();
        $unixTimeCreated = $unixTimeLastModified = $createdUnixTime = $lastModifiedUnixTime = time();
        $lastModified = $created;
        $size = betterSize($folderSize);
        $sizeInBytes = $folderSize;
        $folder = $path;
        $pathToThis = $path."/".$folderName;

        $newPathAndName = exists($pathToThis, $folderName, "folders"); //returns a new path and name if the folder already exists

        if($newPathAndName[0] != $pathToThis){
            $pathToThis = $newPathAndName[0];
            $folderName = $newPathAndName[1];
        }

        mysqli_stmt_bind_param($stmt, "ssssssssssss", $_SESSION['username'], $_SESSION['email'], $folderID, $folderName, $created, $createdUnixTime, $lastModified, $lastModifiedUnixTime, $size, $sizeInBytes, $folder, $pathToThis);
        mysqli_stmt_execute($stmt);

        addLog("PROJECT_FOLDER_UPLOAD_CREATED");

        for($i = 0; $i < $numOfFiles; $i++){
            $fileName =  $_FILES['file-project']['name'][$i];
            $fileType =  $_FILES['file-project']['type'][$i];
            $fileTmpName =  $_FILES['file-project']['tmp_name'][$i];
            $fileError =  $_FILES['file-project']['error'][$i];
            $fileSize =  $_FILES['file-project']['size'][$i];
            $relativePath = $folderName."/".substr($pathArray[$i], strpos($pathArray[$i], "/") + 1); //relative to the folder of the project
            $language =  $langArray[$i]; //determined in javascript (user cannot change it before uploading)

            if($fileError !== 0){
                $_SESSION['folderUploadError'] = "uploadError";

                header("Location: ../account");
                exit();
            }

            $fileSizeInBytes = $fileSize;

            $fileID = base62(25, "files");
            $lastModified = $created; //equal to created because just uploaded
            $fileSize = betterSize($fileSize);
            $type = $fileType;

            $fileIcon = getFileIcon($language);

            $fileDestination = $path."/".$relativePath;
            $folder = $path."/".substr($relativePath, 0, strripos($relativePath, "/"));
            $pathToThis = $fileDestination;

            $sqlQuery = "INSERT INTO files (usernameAuthor, emailAuthor, fileID, name, created, unixTimeCreated, lastModified, unixTimeLastModified, language, size, sizeInBytes, type, fileIcon, folder, pathToThis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            
            if($i == 0){
                mysqli_autocommit($conn, FALSE); //starts transaction (to reduce the execution time of the queries)
            }

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['fileUploadError'] = "sqlError";

                header("Location: ../account");
                exit();
            }
            else{
                mysqli_stmt_bind_param($stmt, "sssssssssssssss", $_SESSION['username'], $_SESSION['email'], $fileID, $fileName, $created, $unixTimeCreated, $lastModified, $unixTimeLastModified, $language, $fileSize, $fileSizeInBytes, $type, $fileIcon, $folder, $pathToThis);
                mysqli_stmt_execute($stmt);
            }

            if($i == $numOfFiles - 1){
                mysqli_commit($conn); //confirms transaction
                mysqli_autocommit($conn, TRUE);
            }
        }

        for($i = 0; $i < $numOfFiles; $i++){ //to calculate the folder size we need all of the files inside it, so we first insert them into the database
            $relativePath = $folderName."/".substr($pathArray[$i], strpos($pathArray[$i], "/") + 1);
            $folder = $path."/".substr($relativePath, 0, strripos($relativePath, "/"));
            createAllFoldersToThis($folder, $created, $createdUnixTime, $folderName);
            $fileDestination = $path."/".$relativePath;
            $fileTmpName = $_FILES['file-project']['tmp_name'][$i];
            uploadFileToS3($fileTmpName, $fileDestination);

            addLog("PROJECT_FILE_UPLOAD");
        }

        updateAllParentFoldersSize($path, $folderSize);

        updateLastModifiedParentFolders($path);

        $sqlQuery = "UPDATE users SET usedStorage=?, usedStorageInBytes=?, filesUploaded=? WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['fileUploadError'] = "sqlError";

            header("Location: ../account");
            exit();
        }
        else{
            $usedStorageInBytes = $user['usedStorageInBytes'] + $folderSize;
            $usedStorage = betterSize($usedStorageInBytes);
            $filesUploaded = $user['filesUploaded'] + $numOfFiles;

            mysqli_stmt_bind_param($stmt, "sssss", $usedStorage, $usedStorageInBytes, $filesUploaded, $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);
        }

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            header("Content-Type: application/json");

            $JSONResponse = array(
                "uploadSuccess" => true,
            );

            echo json_encode($JSONResponse);

            exit();
        }

        $folder = getFolderWithPath($path);

        if(!$folder){
            header("Location: ../account");
            exit();
        }

        header("Location: ../account?folder=".$folder['folderID']);
        exit();
    }
    else if(isset($_POST['add-source-code-multiple-button']) || isset($_FILES['file-multiple'])){
        session_start();

        require("dbh.php");
        require("update-parent-folders-size.php");
        require("add-log.php");
        require("upload-to-s3.php");
        require("std-date.php");
        require("already-exists.php");
        require("get-folder-by-path.php");
        require("check-folder-path.php");
        require("update-last-modified-parent-folders.php");
        require("file-icon.php");

        $numOfFiles = count($_FILES['file-multiple']['name']);
        $langArray = json_decode($_POST['lang-array-multiple']);
        $path = $_POST['path-to-this'];

        if(!checkFolderPath($path)){
            header("Location: ../account");
            exit();
        }

        $filesSize = 0;
        foreach ($_FILES['file-multiple']['size'] as $size) {
            $filesSize += $size;
        }

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['multipleFilesError'] = "sqlError";

            header("Location: ../account");
            exit();
        }
        
        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);
            
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if($filesSize > convertToBytes($user['maxStorage']) - $user['usedStorageInBytes']){
            $_SESSION['multipleFilesError'] = "exceedMaxStorage";

            header("Location: ../account");
            exit();
        }

        for ($i = 0; $i < $numOfFiles; $i++) {
            $fileName =  $_FILES['file-multiple']['name'][$i];
            $fileType =  $_FILES['file-multiple']['type'][$i];
            $fileTmpName =  $_FILES['file-multiple']['tmp_name'][$i];
            $fileError =  $_FILES['file-multiple']['error'][$i];
            $fileSize =  $_FILES['file-multiple']['size'][$i];
            $language =  $langArray[$i]; //determined in javascript (user cannot change it before uploading)

            if($fileError !== 0){
                $_SESSION['multipleFilesError'] = "uploadError";

                header("Location: ../account");
                exit();
            }

            $fileSizeInBytes = $fileSize;

            $fileID = base62(25, "files");
            $created = stdDate();;
            $unixTimeCreated = $unixTimeLastModified = time();
            $lastModified = $created; //equal to created because just uploaded
            $fileSize = betterSize($fileSize);
            $type = $fileType;

            $fileIcon = getFileIcon($language);

            $fileDestination = $path."/".$fileName;

            $newPathAndName = exists($fileDestination, $fileName, "files"); //returns a new path and name if the file already exists

            if($newPathAndName[0] != $fileDestination){
                $fileDestination = $newPathAndName[0];
                $fileName = $newPathAndName[1];
            }

            $folder = $path;
            $pathToThis = $fileDestination;

            $sqlQuery = "INSERT INTO files (usernameAuthor, emailAuthor, fileID, name, created, unixTimeCreated, lastModified, unixTimeLastModified, language, size, sizeInBytes, type, fileIcon, folder, pathToThis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);

            if($i == 0){
                mysqli_autocommit($conn, FALSE);
            }

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['multipleFilesError'] = "sqlError";

                header("Location: ../account");
                exit();
            }
            else{
                mysqli_stmt_bind_param($stmt, "sssssssssssssss", $_SESSION['username'], $_SESSION['email'], $fileID, $fileName, $created, $unixTimeCreated, $lastModified, $unixTimeLastModified, $language, $fileSize, $fileSizeInBytes, $type, $fileIcon, $folder, $pathToThis);
                mysqli_stmt_execute($stmt);
            }

            if($i == $numOfFiles - 1){
                mysqli_commit($conn);
                mysqli_autocommit($conn, TRUE);
            }

            uploadFileToS3($fileTmpName, $fileDestination);
            updateAllParentFoldersSize($folder, $fileSizeInBytes);

            addLog("MULTIPLE_FILES_FILE_UPLOAD");
        }

        updateLastModifiedParentFolders($path);

        $sqlQuery = "UPDATE users SET usedStorage=?, usedStorageInBytes=?, filesUploaded=? WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['multipleFilesError'] = "sqlError";

            header("Location: ../account");
            exit();
        }
        else{
            $usedStorageInBytes = $user['usedStorageInBytes'] + $filesSize;
            $usedStorage = betterSize($usedStorageInBytes);
            $filesUploaded = $user['filesUploaded'] + $numOfFiles;

            mysqli_stmt_bind_param($stmt, "sssss", $usedStorage, $usedStorageInBytes, $filesUploaded, $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);
        }

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            header("Content-Type: application/json");

            $JSONResponse = array(
                "uploadSuccess" => true,
            );

            echo json_encode($JSONResponse);

            exit();
        }

        $folder = getFolderWithPath($path);

        if(!$folder){
            header("Location: ../account");
            exit();
        }

        header("Location: ../account?folder=".$folder['folderID']);
        exit();
    }
    else if(isset($_POST['add-source-code-new-file-button'])){
        session_start();

        require("dbh.php");
        require("add-log.php");
        require("upload-to-s3.php");
        require("std-date.php");
        require("already-exists.php");
        require("get-folder-by-path.php");
        require("check-folder-path.php");
        require("update-last-modified-parent-folders.php");
        require("file-icon.php");

        $fileName = trim($_POST['source-name-new-file']);
        $fileLang = $_POST['language-new-file'];
        $path = $_POST['path-to-this'];

        if(!checkFolderPath($path)){
            header("Location: ../account");
            exit();
        }

        if(empty($fileName) || $fileLang == "0" || strlen($fileName) > 65535){
            if(empty($fileName)){
                $_SESSION['newFileError'] = "fileNameEmpty";
            }
            else if($fileLang == "0"){
                $_SESSION['newFileError'] = "languageNotSelected";
            }
            else{
                $_SESSION['newFileError'] = "fileNameTooLong";
            }

            header("Location: ../account");
            exit();
        } 

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['newFileError'] = "sqlError";

            header("Location: ../account");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);
            
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
        }

        $sqlQuery = "INSERT INTO files (usernameAuthor, emailAuthor, fileID, name, created, unixTimeCreated, lastModified, unixTimeLastModified, language, size, sizeInBytes, type, fileIcon, folder, pathToThis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['newFileError'] = "sqlError";

            header("Location: ../account");
            exit();
        }

        $fileID = base62(25, "files");
        $created = stdDate();;
        $unixTimeCreated = $unixTimeLastModified = time();
        $lastModified = $created;
        $size = "0B";
        $sizeInBytes = 0;
        $type = "application/octet-stream";
        
        $fileIcon = getFileIcon($fileLang);

        $fileDestination = $path."/".$fileName;

        $newPathAndName = exists($fileDestination, $fileName, "files"); //returns a new path and name if the file already exists

        if($newPathAndName[0] != $fileDestination){
            $fileDestination = $newPathAndName[0];
            $fileName = $newPathAndName[1];
        }

        $folder = $path;
        $pathToThis = $fileDestination;

        $tmpPath = "../user-content-tmp/".$fileID;

        $newFile = fopen($tmpPath, "w") or die("Cannot create file!");
        fclose($newFile);

        uploadFileToS3($tmpPath, $pathToThis);

        mysqli_stmt_bind_param($stmt, "sssssssssssssss", $_SESSION['username'], $_SESSION['email'], $fileID, $fileName, $created, $unixTimeCreated, $lastModified, $unixTimeLastModified, $fileLang, $size, $sizeInBytes, $type, $fileIcon, $folder, $pathToThis);
        mysqli_stmt_execute($stmt);

        addLog("NEW_FILE_CREATED");

        updateLastModifiedParentFolders($path);

        $folder = getFolderWithPath($path);

        if(!$folder){
            header("Location: ../account");
            exit();
        }

        header("Location: ../account?folder=".$folder['folderID']);
        exit();
    }
    else if(isset($_POST['add-source-code-new-folder-button'])){

        session_start();

        require("dbh.php");
        require("add-log.php");
        require("std-date.php");
        require("already-exists.php");
        require("get-folder-by-path.php");
        require("check-folder-path.php");
        require("update-last-modified-parent-folders.php");

        $folderName = trim($_POST['source-name-new-folder']);
        $path = $_POST['path-to-this'];

        if(!checkFolderPath($path)){
            header("Location: ../account");
            exit();
        }

        if(empty($folderName) || strlen($folderName) > 65535){
            if(empty($folderName)){
                $_SESSION['newFolderError'] = "folderNameEmpty";
            }
            else{
                $_SESSION['newFolderError'] = "folderNameTooLong";
            }

            header("Location: ../account");
            exit();
        }

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['newFolderError'] = "sqlError";

            header("Location: ../account");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);
            
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
        }

        $sqlQuery = "INSERT INTO folders (usernameAuthor, emailAuthor, folderID, name, created, createdUnixTime, lastModified, lastModifiedUnixTime, size, sizeInBytes, folder, pathToThis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
    
        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['newFolderError'] = "sqlError";

            header("Location: ../account");
            exit();
        }

        $folderID = base62(25, "folders");
        $created = stdDate();
        $createdUnixTime = $lastModifiedUnixTime = time();
        $lastModified = $created;
        $size = "0B";
        $sizeInBytes = 0;
        $folder = $path;
        $pathToThis = $path."/".$folderName;

        $newPathAndName = exists($pathToThis, $folderName, "folders"); //returns a new path and name if the folder already exists

        if($newPathAndName[0] != $pathToThis){
            $pathToThis = $newPathAndName[0];
            $folderName = $newPathAndName[1];
        }

        mysqli_stmt_bind_param($stmt, "ssssssssssss", $_SESSION['username'], $_SESSION['email'], $folderID, $folderName, $created, $createdUnixTime, $lastModified, $lastModifiedUnixTime, $size, $sizeInBytes, $folder, $pathToThis);
        mysqli_stmt_execute($stmt);

        addLog("NEW_FOLDER_CREATED");

        updateLastModifiedParentFolders($path);
    
        $folder = getFolderWithPath($path);

        if(!$folder){
            header("Location: ../account");
            exit();
        }

        header("Location: ../account?folder=".$folder['folderID']);
        exit();
    }
    else{
        header("Location: ../");
        exit();
    }

    function base62($strLength, $table){
        $charset = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $uuid = "";

        for($i = 0; $i < $strLength; $i++){
            $uuid .= $charset[mt_rand(0, 61)];
        }

        require("dbh.php");

        if($table == "files"){
            $sqlQuery = "SELECT * FROM files WHERE fileID=?";
        }
        else{
            $sqlQuery = "SELECT * FROM folders WHERE folderID=?";
        }

        $stmtBase = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmtBase, $sqlQuery)){
            $sqlError = "sqlError";
            
            header("Location: ../");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmtBase, "s", $uuid);
            mysqli_stmt_execute($stmtBase);
            mysqli_stmt_store_result($stmtBase);

            $resultNum = mysqli_stmt_num_rows($stmtBase);

            if($resultNum > 0){
                $uuid = base62($strLength, $table);
            }
        }

        return $uuid;
    }

    function betterSize($fileSize){

        for($i = 0; $fileSize >= 1024; $i++){
            $fileSize /= 1024;
        }

        $fileSize = round($fileSize);

        switch ($i) {
            case 0:
                $betterSize = $fileSize."B ";
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

    function convertToBytes($fileSize){
        $fileSizeUnitPriority = fileSizeUnitPriority(substr($fileSize, strlen($fileSize) - 2));
        $fileSizePure = substr($fileSize, 0, strlen($fileSize) - 2);

        for($i = 0; $i < $fileSizeUnitPriority; $i++){
            $fileSizePure *= 1024;
        }

        $fileSizeInBytes = $fileSizePure;

        return $fileSizeInBytes;
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

    function createAllFoldersToThis($folderPath, $created, $createdUnixTime, $uploadedFolder){

        require("dbh.php");
        require_once("exists-path.php");

        $folders = explode("/", $folderPath);
        $numOfFolders = count($folders);
        $uploadedFolderIndex = array_search($uploadedFolder, $folders);

        for($i = $uploadedFolderIndex; $i < $numOfFolders; $i++){
            $folder = "";
            for($j = 0; $j <= $i; $j++){
                if($j > 0){
                    $folder .= "/".$folders[$j];
                }
                else{
                    $folder .= $folders[$j];
                }
            }

            if(!existsPath($folder, "folders")){
                $sqlQuery = "INSERT INTO folders (usernameAuthor, emailAuthor, folderID, name, created, createdUnixTime, lastModified, lastModifiedUnixTime, size, sizeInBytes, folder, pathToThis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                
                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $_SESSION['folderUploadError'] = "sqlError";

                    header("Location: ../account");
                    exit();
                }

                $folderName = explode("/", $folder)[count(explode("/", $folder)) - 1];

                $folderID = base62(25, "folders");
                $lastModified = $created;
                $lastModifiedUnixTime = $createdUnixTime;
                $folderSize = folderSize($folderPath);
                $size = betterSize($folderSize);
                $sizeInBytes = $folderSize;
                $pathToThis = $folder;
                $folder = substr($folder, 0, strripos($folder, "/"));

                mysqli_stmt_bind_param($stmt, "ssssssssssss", $_SESSION['username'], $_SESSION['email'], $folderID, $folderName, $created, $createdUnixTime, $lastModified, $lastModifiedUnixTime, $size, $sizeInBytes, $folder, $pathToThis);
                mysqli_stmt_execute($stmt);
            }
        }
    }

    function folderSize($path){
        
        require("dbh.php");

        $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND folder LIKE '{$path}%'";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['folderUploadError'] = "sqlError";

            header("Location: ../account");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $files = mysqli_stmt_get_result($stmt);

        $folderSize = 0;

        foreach ($files as $file) {
            $folderSize += $file['sizeInBytes'];
        }

        return $folderSize;
    }
?>