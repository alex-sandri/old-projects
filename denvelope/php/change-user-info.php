<?php
    if(isset($_POST['account-settings-change-button-username'])){

        session_start();

        require("dbh.php");
        require("add-log.php");
        require("update-user-username.php");

        $newUsername = trim($_POST['username-account-settings']);

        $pattern = "/^[a-zA-Z0-9\.\-_ ]*$/";

        if(empty($newUsername) || strlen($newUsername) < 4 || strlen($newUsername) > 15){
            if(empty($newUsername)){
                $_SESSION['newUsernameError'] = "emptyField";
            }
            else if(strlen($newUsername) < 4){
                $_SESSION['newUsernameError'] = "tooShort";
            }
            else{
                $_SESSION['newUsernameError'] = "tooLong";
            }

            header("Location: ../account/settings");
            exit();
        }
        else if(!preg_match($pattern, $newUsername)){
            $_SESSION['newUsernameError'] = "notValidUsername";

            header("Location: ../account/settings");
            exit();
        }

        updateUserUsername($newUsername);

        $sqlQuery = "DELETE FROM sessions WHERE (username=? OR email=?) AND sessionID!=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['newEmailError'] = "sqlError";

            header("Location: ../account/settings");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_COOKIE['userSession']);
        mysqli_stmt_execute($stmt);

        addLog("USERNAME_CHANGE (".$_SESSION['username']." => ".$newUsername.")");

        header("Location: ../account/settings");
        exit();
    }
    else if(isset($_POST['account-settings-change-button-email'])){
        
        session_start();

        require("dbh.php");
        require("add-log.php");
        require("send-email-email-changed.php");
        require("update-user-email.php");

        $newEmail = trim($_POST['email-account-settings']);

        if(empty($newEmail) || strlen($newEmail) > 255){
            if(empty($newUsername)){
                $_SESSION['newEmailError'] = "emptyField";
            }
            else{
                $_SESSION['newEmailError'] = "tooLong";
            }

            header("Location: ../account/settings");
            exit();
        }
        else if(!filter_var($newEmail, FILTER_VALIDATE_EMAIL)){
            $_SESSION['newEmailError'] = "notValidEmail";

            header("Location: ../account/settings");
            exit();
        }

        updateUserEmail($newEmail);

        $sqlQuery = "DELETE FROM password_reset WHERE email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['newEmailError'] = "sqlError";

            header("Location: ../account/settings");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $sqlQuery = "DELETE FROM sessions WHERE (username=? OR email=?) AND sessionID!=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['newEmailError'] = "sqlError";

            header("Location: ../account/settings");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_COOKIE['userSession']);
        mysqli_stmt_execute($stmt);

        addLog("EMAIL_CHANGE (".$_SESSION['email']." => ".$newEmail.")");

        sendEmailEmailChanged($_SESSION['email'], $newEmail);

        header("Location: ../account/settings");
        exit();
    }
    else if(isset($_POST['change-password-button'])){

        session_start();

        require("dbh.php");
        require("add-log.php");

        $oldPassword = trim($_POST['current-password']);
        $newPassword = trim($_POST['new-password']);

        if($oldPassword == $newPassword){
            $_SESSION['newPasswordError'] = "equalPasswords";

            header("Location: ../account/settings/#security");
            exit();
        }

        if(empty($oldPassword) || empty($newPassword)){
            if(empty($oldPassword)){
                $_SESSION['newPasswordError'] = "emptyFieldOld";
            }
            else{
                $_SESSION['newPasswordError'] = "emptyFieldNew";
            }

            header("Location: ../account/settings/#security");
            exit();
        }
        else if(strlen($oldPassword) < 8 || strlen($newPassword) < 8){
            if(strlen($oldPassword) < 8){
                $_SESSION['newPasswordError'] = "tooShortOld";
            }
            else{
                $_SESSION['newPasswordError'] = "tooShortNew";
            }

            header("Location: ../account/settings/#security");
            exit();
        }

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['newPasswordError'] = "sqlError";

            header("Location: ../account/settings/#security");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        $passwordCheck = password_verify($oldPassword, $user['pwd']);

        if(!$passwordCheck){

            $_SESSION['newPasswordError'] = "wrongOldPassword";

            header("Location: ../account/settings/#security");
            exit();
        }

        $sqlQuery = "UPDATE users SET pwd=? WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['newPasswordError'] = "sqlError";

            header("Location: ../account/settings/#security");
            exit();
        }

        $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);

        mysqli_stmt_bind_param($stmt, "sss", $newPasswordHashed, $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $sqlQuery = "DELETE FROM sessions WHERE (username=? OR email=?) AND sessionID!=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['newPasswordError'] = "sqlError";

            header("Location: ../account/settings/#security");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_COOKIE['userSession']);
        mysqli_stmt_execute($stmt);

        addLog("PASSWORD_CHANGE");

        header("Location: ../account/settings/#security");
        exit();
    }
    else{
        header("Location: ../account/settings");
        exit();
    }
?>