<?php
    if(isset($_POST['signup-button']) || isset($_POST['signup-button-alt'])){

        session_start();

        //database connection
        require("dbh.php");
        require_once("base62.php");
        require("send-email-ses.php");
        require("std-date.php");
        require("create-signup-email.php");
        require("global-vars.php");
        require("insert-into-email-preferences.php");
        require("verify-recaptcha.php");

        $preferredLanguage = $lang;
        require("../lang/".$preferredLanguage.".php");

        //get user inputs from the form
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $reCAPTCHAResponse = $_POST['g-recaptcha-response'];

        //recover input data in the form if an error occurs (not the password)
        $_SESSION['usernameField'] = $username;
        $_SESSION['emailField'] = $email;

        //if an error during login has occured (for changing form section to login on the home page)
        $_SESSION['signupError'] = false;

        //username char pattern
        $pattern = "/^[a-zA-Z0-9\.\-_]*$/";

        if(!reCAPTCHAVerify($reCAPTCHAResponse)){
            $_SESSION['recaptchaError'] = "invalid";
            $_SESSION['signupError'] = true;
        }

        if(empty($username) || empty($email) || empty($password)){

            if(empty($username)){
                $_SESSION['usernameError'] = "emptyField";
            }
            if(empty($email)){
                $_SESSION['emailError'] = "emptyField";
            }
            if(empty($password)){
                $_SESSION['passwordError'] = "emptyField";
            }

            $_SESSION['signupError'] = true;
        }
        if(strlen($username) < 4){
            $_SESSION['usernameError'] = "usernameTooShort";
            $_SESSION['signupError'] = true;
        }
        if(strlen($username) > 15){
            $_SESSION['usernameError'] = "usernameTooLong";
            $_SESSION['signupError'] = true;
        }
        if(strlen($email) > 255){
            $_SESSION['emailError'] = "emailTooLong";
            $_SESSION['signupError'] = true;
        }
        if(strlen($password) < 8 && !empty($password)){
            $_SESSION['passwordError'] = "passwordTooShort";
            $_SESSION['signupError'] = true;
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match($pattern, $username)){
            $_SESSION['emailError'] = "invalidEmail";
            $_SESSION['usernameError'] = "invalidUsername";
            $_SESSION['signupError'] = true;
        }
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $_SESSION['emailError'] = "invalidEmail";
            $_SESSION['signupError'] = true;
        }
        else if(!preg_match($pattern, $username)){
            $_SESSION['usernameError'] = "invalidUsername";
            $_SESSION['signupError'] = true;
        }

        //if some error occured the user will be redirected to the home page
        if($_SESSION['signupError']){
            header("Location: ../");
            exit();
        }

        else{

            $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $sqlError = "sqlError";

                header("Location: ../");
                exit();
            }
            else{
                mysqli_stmt_bind_param($stmt, "ss", $username, $email);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);

                $resultNum = mysqli_stmt_num_rows($stmt);

                //check if username or email already exist
                if($resultNum > 0){

                    //if number of results > 1 then username and email already exist
                    if($resultNum > 1){

                        $_SESSION['usernameError'] = "alreadyTaken";
                        $_SESSION['emailError'] = "alreadyTaken";
                    }
                    //if number of results == 1 then username or email already exist
                    else if($resultNum == 1){

                        $sqlQuery = "SELECT * FROM users WHERE username=?";
                        $stmt = mysqli_stmt_init($conn);

                        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){

                            $sqlError = "sqlError";
            
                            header("Location: ../");
                            exit();
                        }
                        else{
                            mysqli_stmt_bind_param($stmt, "s", $username);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_store_result($stmt);

                            $resultNum = mysqli_stmt_num_rows($stmt);

                            //if number of results > 0 then username already exists
                            if($resultNum > 0){
                                
                                $_SESSION['usernameError'] = "alreadyTaken";
                            }
                            //else email is already taken
                            else{
                                
                                $_SESSION['emailError'] = "alreadyTaken";
                            }
                        }
                    }

                    header("Location: ../");
                    exit();
                }
                //else user gets signed up
                else{

                    $sqlQuery = "INSERT INTO users (username, email, pwd, userID, created, createdUnixTime, plan, maxStorage, usedStorage, preferredLanguage) VALUES (?, ?, ?, ?, ?, ?, 'Free', '$FREE_TIER_STORAGE', '0B', ?)";
                    $stmt = mysqli_stmt_init($conn);

                    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){

                        $sqlError = "sqlError";
            
                        header("Location: ../");
                        exit();
                    }
                    else{

                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                        $uuid = base62(25, "users", "userID");

                        $created = stdDate();
                        $createdUnixTime = time();

                        mysqli_stmt_bind_param($stmt, "sssssss", $username, $email, $hashedPassword, $uuid, $created, $createdUnixTime, $preferredLanguage);
                        mysqli_stmt_execute($stmt);
                        
                        $_SESSION['signupSuccess'] = "success";

                        $sqlQuery = "INSERT INTO logs (username, email, logType, logTime) VALUES (?, ?, ?, ?)";
                        $stmt = mysqli_stmt_init($conn);

                        $logType = "SIGNUP";

                        if(mysqli_stmt_prepare($stmt, $sqlQuery)){
                            mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $logType, $created);
                            mysqli_stmt_execute($stmt);
                        }

                        insertIntoEmailPreferences($username, $email);

                        $subject = getTranslatedContent("email_signup_subject");

                        $plaintext_body = getTranslatedContent("email_signup_welcome")."\r\n";
                        $plaintext_body .= getTranslatedContent("email_signup_excited_to_have_you")."\r\n";
                        $plaintext_body .= getTranslatedContent("email_signup_last_step");
                        $plaintext_body .= getTranslatedContent("email_signup_click_this_link").":\r\n";
                        $plaintext_body .= "https://denvelope.com/confirm/?u=".$uuid."&t=".$createdUnixTime;
                        $plaintext_body .= "\r\n\r\n".getTranslatedContent("email_signup_have_question_shoot_us_email").":\r\n";
                        $plaintext_body .= "support@denvelope.com";
                        $plaintext_body .= "\r\n\r\n".getTranslatedContent("email_signup_contact_us_website_form").":\r\n";
                        $plaintext_body .= "https://denvelope.com/contact";
                        $plaintext_body .= "\r\n\r\n".getTranslatedContent("email_signup_received_by_mistake")."\r\n\r\n";
                        $plaintext_body .= getTranslatedContent("email_all_the_best")."\r\n";
                        $plaintext_body .= getTranslatedContent("email_the_denvelope_team");

                        $html_body = createSignUpEmail($uuid, $createdUnixTime);

                        sendEmailSES($email, $subject, $plaintext_body, $html_body);

                        if(isset($_SESSION['betaTestKeyOnReaddress']) && $_SESSION['betaTestKeyOnReaddress'] == true){
                            header("Location: ../?betakey=".$_SESSION['betaKey']);
                            exit();
                        }

                        header("Location: ../");
                        exit();
                    }
                }
            }
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
    else{
        header("Location: ../");
        exit();
    }
?>