<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_POST['username-email'])){
        session_start();

        require("dbh.php");

        $usernameEmail = $_POST['username-email'];

        $sqlQuery = "SELECT * FROM authy_users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $usernameEmail, $usernameEmail);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if(!$user){
            $has2FA = false;
        }
        else{
            $has2FA = true;
        }

        $_SESSION['has2FA'] = $has2FA;

        header('Content-type: application/json');

        $JSONdata = array($_SESSION);
        echo json_encode($JSONdata);

        exit();
    }

    function has2FA(){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM authy_users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if(!$user){
            $has2FA = false;
        }
        else{
            $has2FA = true;
        }

        return $has2FA;
    }

    function has2FAOnLogin($usernameEmail){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM authy_users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $usernameEmail, $usernameEmail);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if(!$user){
            $has2FA = false;
        }
        else{
            $has2FA = true;
        }

        return $has2FA;
    }
?>