<?php
    if(isset($_POST['resend-confirm-email-button'])){
        session_start();

        require("dbh.php");
        require("create-signup-email.php");
        require("send-email-ses.php");

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['usernameEmailNotActivatedAccount'], $_SESSION['usernameEmailNotActivatedAccount']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        $subject = "Confirm your Denvelope account";

        $plaintext_body = "Welcome to Denvelope!\r\n";
        $plaintext_body .= "We're excited to have you here.\r\n";
        $plaintext_body .= "But there's just one last step before you can see what we offer and hopefully enjoy that.";
        $plaintext_body .= "Just click this link and you're done: ";
        $plaintext_body .= "https://denvelope.com/confirm/?u=".$user['userID']."&t=".$user['createdUnixTime'];
        $plaintext_body .= "\r\n\r\nIf you have any question feel free to shoot us an email at: ";
        $plaintext_body .= "support@denvelope.com";
        $plaintext_body .= "\r\n\r\nor if you prefer to use the contact form on our website: ";
        $plaintext_body .= "https://denvelope.com/contact";
        $plaintext_body .= "\r\n\r\nIf you believe you received this email by mistake just ignore this\r\n\r\n";
        $plaintext_body .= "All the best,\r\nThe Denvelope Team";

        $html_body = createSignUpEmail($user['userID'], $user['createdUnixTime']);

        sendEmailSES($user['email'], $subject, $plaintext_body, $html_body);

        unset($_SESSION['usernameEmailNotActivatedAccount']);
        $_SESSION['emailResent'] = true;

        if(isset($_SESSION['betaTestKeyOnReaddress']) && $_SESSION['betaTestKeyOnReaddress'] == true){
            header("Location: ../?betakey=".$_SESSION['betaKey']);
            exit();
        }

        header("Location: ../");
        exit();
    }
    else{
        header("Location: ../");
        exit();
    }
?>