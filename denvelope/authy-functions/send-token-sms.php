<?php
    // Disabled
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_POST['username-email-sms']) || false){
        session_start();

        require("../php/has-2fa.php");

        $usernameEmail = $_POST['username-email-sms'];

        if(has2FAOnLogin($usernameEmail)){
            sendTokenSMS($usernameEmail);

            $_SESSION['2FACodeSent'] = true;
        }
        else{
            $_SESSION['2FACodeSent'] = "not-needed";
        }

        header('Content-type: application/json');

        $JSONdata = array($_SESSION);
        echo json_encode($JSONdata);

        exit();
    }

    function sendTokenSMS($usernameEmail){
        require("api-key.php");
        require("../php/dbh.php");
        require("../php/get-user-preferred-language.php");

        $authyAPI = authyAPI();
        
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

        $sms = $authyAPI->requestSms($user['authyID'], array(
            "force" => "true",
            "locale" => getUserPreferredLanguageUsernameEmail($usernameEmail)
        ));
    }
?>