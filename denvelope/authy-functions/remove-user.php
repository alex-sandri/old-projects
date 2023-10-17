<?php
    // Disabled
    if(isset($_POST['two-factor-auth-remove-button']) || false){
        session_start();

        require("api-key.php");
        require("../php/dbh.php");
        require("../php/get-2fa-user.php");

        $authyAPI = authyAPI();

        $user = get2FAUser();

        $result = $authyAPI->deleteUser($user['authyID']);

        $sqlQuery = "DELETE FROM authy_users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }
    
        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        header("Location: ../account/settings/#security");
        exit();
    }

    function removeAuthyUser(){
        require_once("api-key.php");
        require("../php/dbh.php");
        require_once("../php/get-2fa-user.php");

        $authyAPI = authyAPI();

        $user = get2FAUser();

        $result = $authyAPI->deleteUser($user['authyID']);

        $sqlQuery = "DELETE FROM authy_users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }
    
        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);
    }
?>