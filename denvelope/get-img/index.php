<?php
    if(isset($_GET['id'])){
        session_start();

        require("../php/get-file.php");
        require("../php/get-img-from-s3.php");

        $file = getFile($_GET['id']);
        
        if(!$file){
            header("Location: ../");
            exit();
        }

        if(strpos($file['type'], "image") === false){
            header("Location: ../");
            exit();
        }

        getImgFromS3($file['pathToThis'], $file['type']);
    }
    else{
        header("Location: ../");
        exit();
    }
?>