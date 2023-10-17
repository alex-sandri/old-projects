<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        session_start();

        require("create-cookie.php");
        require("update-user-preferred-language.php");

        $language = $_POST['language'];

        if($language != "en" && $language != "it"){
            $_SESSION['languageUpdated'] = false;

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }

        createCookie("lang", $language, 60);

        if(!isset($_POST['from-footer-change'])){
            updatePreferredLanguage($language);
        }

        $_SESSION['languageUpdated'] = true;

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