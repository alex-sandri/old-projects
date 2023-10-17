<?php
    function getUserPreferredLanguage(){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        return $user['preferredLanguage'];
    }

    function getUserPreferredLanguageByEmail($email){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM users WHERE email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        return $user['preferredLanguage'];
    }

    function getUserPreferredLanguageUsernameEmail($usernameEmail){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $usernameEmail, $usernameEmail);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        return $user['preferredLanguage'];
    }

    function getUserPreferredLanguageForStripeLocale(){
        return $STRIPE_CUSTOMER_PREFERRED_LOCALE = array(
            "en" => "en-US",
            "it" => "it-IT",
        )[getUserPreferredLanguage()];
    }
?>