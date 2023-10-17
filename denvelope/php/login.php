<?php
    if(isset($_POST['login-button']) || isset($_POST['login-button-alt'])){

        session_start();

        //database connection
        require("dbh.php");

        require("base62.php");
        require("std-date.php");
        require("add-log.php");
        require("global-vars.php");
        require("set-cookie.php");
        require("send-email-new-login.php");
        require("send-email-new-login-no-cookie.php");
        require("verify-recaptcha.php");
        require("../authy-functions/verify-token.php");
        require("has-2fa.php");
        require("get-location.php");
        require("get-os.php");
        require("get-browser.php");

        //get user inputs from the form
        $usernameEmail = trim($_POST['username-email']);
        $password = trim($_POST['password']);

        if($isProduction){
            $reCAPTCHAResponse = $_POST['g-recaptcha-response'];
        }

        $authyToken = $_POST['2fa-code'];
        $authyTokenPattern = "/^\d+$/"; //only numbers are allowed

        if(isset($_POST['remember-me-login']) && $_POST['remember-me-login'] == "true"){
            $rememberMe = true;
        }
        else{
            $rememberMe = false;
        }

        //recover input data in the form if an error occurs (not the password)
        $_SESSION['username-emailField'] = $usernameEmail;

        //if an error during login has occured (for changing form section to login on the home page)
        $_SESSION['loginError'] = false;

        if($isProduction){
            if(!reCAPTCHAVerify($reCAPTCHAResponse)){
                $_SESSION['recaptchaError'] = "invalid";
                $_SESSION['loginError'] = true;
            }
        }

        if(has2FAOnLogin($usernameEmail)){
            if(empty($authyToken)){
                $_SESSION['2FACodeError'] = "emptyField";
                $_SESSION['loginError'] = true;
            }
            else if(!preg_match($authyTokenPattern, $authyToken)){
                $_SESSION['2FACodeError'] = "invalidCode";
                $_SESSION['loginError'] = true;
            }
            else if(strlen($authyToken) > 7){
                $_SESSION['2FACodeError'] = "tooLongCode";
                $_SESSION['loginError'] = true;
            }
            else if(strlen($authyToken) < 7){
                $_SESSION['2FACodeError'] = "tooShortCode";
                $_SESSION['loginError'] = true;
            }
            else if(!verifyAuthyToken($authyToken, $usernameEmail)){
                $_SESSION['2FACodeError'] = "wrongCode";
                $_SESSION['loginError'] = true;
            }
        }

        if(empty($usernameEmail) || empty($password)){

            if(empty($usernameEmail)){
                $_SESSION['usernameEmailError'] = "emptyField";
                $_SESSION['loginError'] = true;
            }
            if(empty($password)){
                $_SESSION['passwordLoginError'] = "emptyField";
                $_SESSION['loginError'] = true;
            }
        }
        if(strlen($usernameEmail) > 255){
            $_SESSION['usernameEmailError'] = "invalidUsernameEmail";
            $_SESSION['loginError'] = true;
        }
        if(strlen($password) < 8 && !empty($password)){
            $_SESSION['passwordLoginError'] = "invalidPassword";
            $_SESSION['loginError'] = true;
        }

        if((isset($_POST['from']) && $_POST['from'] == "login-page") && $_SESSION['loginError']){
            header("Location: ../login");
            exit();
        }

        //if some error occured the user will be redirected to the home page
        if($_SESSION['loginError']){
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
                mysqli_stmt_bind_param($stmt, "ss", $usernameEmail, $usernameEmail);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);

                if($row){

                    if($row['activated'] == 0){
                        $_SESSION['loginError'] = "accountNotActivated";
                        $_SESSION['usernameEmailNotActivatedAccount'] = $usernameEmail;

                        if((isset($_POST['from']) && $_POST['from'] == "login-page")){
                            header("Location: ../login");
                            exit();
                        }

                        if(isset($_SESSION['betaTestKeyOnReaddress']) && $_SESSION['betaTestKeyOnReaddress'] == true){
                            header("Location: ../?betakey=".$_SESSION['betaKey']);
                            exit();
                        }

                        header("Location: ../");
                        exit();
                    }

                    $passwordCheck = password_verify($password, $row['pwd']);

                    if(!$passwordCheck){
                        $_SESSION['passwordLoginError'] = "wrongPassword";
                        $_SESSION['loginError'] = true;

                        if((isset($_POST['from']) && $_POST['from'] == "login-page")){
                            header("Location: ../login");
                            exit();
                        }

                        if(isset($_SESSION['betaTestKeyOnReaddress']) && $_SESSION['betaTestKeyOnReaddress'] == true){
                            header("Location: ../?betakey=".$_SESSION['betaKey']);
                            exit();
                        }

                        header("Location: ../");
                        exit();
                    }
                    else{

                        if($rememberMe){
                            $sessionID = bin2hex(random_bytes(64));
                            
                            $sqlQuery = "INSERT INTO sessions (username, email, sessionID, IPAddress, OS, browser, lastActivity, unixTime, sessionLogoutID, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                $sqlError = "sqlError";

                                header("Location: ../");
                                exit();
                            }
                            else{

                                if($isProduction){
                                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; //REMOTE_ADDR returns the ELB IP, so HTTP_X_FORWARDED_FOR is needed
                                }
                                else{
                                    $ip = $_SERVER['REMOTE_ADDR'];
                                }

                                $os = getOS();
                                $browser = getBrowser();
                                $lastActivity = stdDate();
                                $unixTime = time();
                                $sessionLogoutID = base62(50, "sessions", "sessionLogoutID");
                                $location = getLocation($ip);

                                $location = $location['geo']['city'].", ".$location['geo']['country-name'];

                                mysqli_stmt_bind_param($stmt, "ssssssssss", $row['username'], $row['email'], $sessionID, $ip, $os, $browser, $lastActivity, $unixTime, $sessionLogoutID, $location);
                                mysqli_stmt_execute($stmt);
                                
                                $_SESSION['username'] = $row['username'];
                                $_SESSION['email'] = $row['email'];

                                $_SESSION['loginSuccess'] = "success";

                                if($isProduction){
                                    setSecureCookie($sessionID);
                                }   
                                else{
                                    setcookie("userSession", $sessionID, time() + 86400 * 30, "/", "");
                                }

                                $logType = "LOGIN_COOKIE";

                                if($isProduction){
                                    sendEmailNewLogin($row['email'], $os, $browser, $ip, $lastActivity, $sessionLogoutID, $location);
                                }
                            }
                        }
                        else{
                            $_SESSION['username'] = $row['username'];
                            $_SESSION['email'] = $row['email'];

                            $_SESSION['loginSuccess'] = "success";

                            $logType = "LOGIN_NO_COOKIE";

                            if($isProduction){
                                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                            }
                            else{
                                $ip = $_SERVER['REMOTE_ADDR'];
                            }

                            $os = getOS();
                            $browser = getBrowser();
                            $lastActivity = stdDate();
                            $unixTime = time();
                            $location = getLocation($ip);

                            $location = $location['geo']['city'].", ".$location['geo']['country-name'];

                            if($isProduction){
                                sendEmailNewLoginNoCookie($row['email'], $os, $browser, $ip, $lastActivity, "", $location); //sessionLogoutID not available right now
                            }
                        }

                        addLog($logType);

                        if(isset($_GET['ref'])){
                            header("Location: ../".$_GET['ref']);
                            exit();
                        }

                        if(isset($_SESSION['betaTestKeyOnReaddress']) && $_SESSION['betaTestKeyOnReaddress'] == true){
                            header("Location: ../?betakey=".$_SESSION['betaKey']);
                            exit();
                        }

                        header('Location: ../');
                        exit();
                    }
                }
                else{
                    $_SESSION['usernameEmailError'] = "usernameEmailDoesNotExist";
                    $_SESSION['loginError'] = true;

                    header("Location: ../");
                    exit();
                }
            }
        }
    }
    else{
        header("Location: ../");
        exit();
    }
?>