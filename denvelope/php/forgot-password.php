<?php
    if(isset($_POST['password-reset-button'])){

        session_start();

        //database connection
        require("dbh.php");
        require("send-email-ses.php");
        require("create-forgot-password-email.php");
        require("get-user-preferred-language.php");

        //get user inputs from the form
        $email = trim($_POST['pwdResetEmail']);

        //if an error during password reset has occured (for changing form section to password reset on the home page)
        $_SESSION['passwordResetError'] = false;

        if(empty($email)){
            $_SESSION['pwdResetEmailError'] = "emptyField";
            $_SESSION['passwordResetError'] = true;

            header("Location: ../forgot");
            exit();
        }
        else if(strlen($email) > 255){
            $_SESSION['pwdResetEmailError'] = "emailTooLong";
            $_SESSION['passwordResetError'] = true;

            header("Location: ../forgot");
            exit();
        }
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $_SESSION['pwdResetEmailError'] = "invalidEmail";
            $_SESSION['passwordResetError'] = true;

            header("Location: ../forgot");
            exit();
        }
        else{
            $selector = bin2hex(random_bytes(16));
            $validator = bin2hex(random_bytes(32));

            //expires after 10 minutes
            $expire = date("U") + 600;

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

            if(!$user){
                $_SESSION['pwdResetEmailError'] = "emailDoesNotExist";
                $_SESSION['passwordResetError'] = true;

                header("Location: ../forgot");
                exit();
            }

            //delete previous password reset request if the user asked more than one time
            $sqlQuery = "DELETE FROM password_reset WHERE email=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $sqlError = "sqlError";

                header("Location: ../");
                exit();
            }
            else{
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);

                $sqlQuery = "INSERT INTO password_reset (username, email, selector, validator, expire) VALUES (?, ?, ?, ?, ?)";

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $sqlError = "sqlError";
    
                    header("Location: ../");
                    exit();
                }
                else{
                    $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);

                    mysqli_stmt_bind_param($stmt, "sssss", $user['username'], $email, $selector, $hashedValidator, $expire);
                    mysqli_stmt_execute($stmt);
                }
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            $lang = getUserPreferredLanguageByEmail($email);
            require("../lang/".$lang.".php");

            $subject = getTranslatedContent("email_password_reset_subject");

            $plaintext_body = getTranslatedContent("email_password_reset_reset_your_password")."\r\n";
            $plaintext_body .= getTranslatedContent("email_password_reset_password_reset_request_received")."\r\n";
            $plaintext_body .= getTranslatedContent("email_password_reset_click_link_to_reset").":\r\n";
            $plaintext_body .= "https://denvelope.com/reset/?s=".$selector."&v=".$validator;
            $plaintext_body .= "\r\n".getTranslatedContent("email_password_reset_link_expires_in").".";
            $plaintext_body .= "\r\n".getTranslatedContent("email_password_reset_have_question_shoot_us_email").":\r\n";
            $plaintext_body .= "support@denvelope.com";
            $plaintext_body .= "\r\n\r\n".getTranslatedContent("email_password_reset_contact_us_website_form").":\r\n";
            $plaintext_body .= "https://denvelope.com/contact";
            $plaintext_body .= "\r\n\r\n".getTranslatedContent("email_password_reset_did_not_request_password_will_not_change")."\r\n\r\n";
            $plaintext_body .= getTranslatedContent("email_all_the_best")."\r\n";
            $plaintext_body .= getTranslatedContent("email_the_denvelope_team");

            $html_body = createForgotPasswordEmail($selector, $validator);

            sendEmailSES($email, $subject, $plaintext_body, $html_body);

            $_SESSION['resetPasswordEmailSent'] = true;

            if(isset($_SESSION['betaTestKeyOnReaddress']) && $_SESSION['betaTestKeyOnReaddress'] == true){
                header("Location: ../?betakey=".$_SESSION['betaKey']);
                exit();
            }

            header("Location: ../");
            exit();
        }
    }
    else{
        header("Location: ../");
        exit();
    }
?>