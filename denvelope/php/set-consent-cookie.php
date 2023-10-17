<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        session_start();

        require("create-cookie.php");

        createCookie("consent", "true", 60);

        $_SESSION['consentCookie'] = true;

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