<?php
    if(isset($_POST['add-source-code-save-button-single'])){

        require("dbh.phpp");

        $file = $_FILES['file-single'];

        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];
    }
    else{
        header("Location: ../");
        exit();
    }
?>