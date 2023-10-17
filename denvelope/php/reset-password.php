<?php
    if(isset($_POST['new-password-button'])){

        session_start();

        require("dbh.php");
        require("logout-from-all-devices.php");
        require("send-email-password-changed.php");

        $selector = trim($_POST['selector']);
        $validator = trim($_POST['validator']);
        $password = trim($_POST['new-password']);
        $repeatPassword = trim($_POST['repeat-new-password']);

        if(empty($selector) || empty($validator)){
            header("Location: ../");
            exit();
        }
        else if(!ctype_xdigit($selector) || !ctype_xdigit($validator)){
            header("Location: ../");
            exit();
        }

        if(empty($password) || empty($repeatPassword)){
            if(empty($password)){
                $_SESSION['resetPasswordError'] = "emptyNewPassword";
            }
            else{
                $_SESSION['resetPasswordError'] = "emptyRepeatPassword";
            }

            header("Location: ../forgot/?s=$selector&v=$validator");
            exit();
        }
        else if(strlen($password) < 8){
            $_SESSION['resetPasswordError'] = "passwordTooShort";

            header("Location: ../forgot/?s=$selector&v=$validator");
            exit();
        }
        else if($password != $repeatPassword){
            $_SESSION['resetPasswordError'] = "passwordsDoNotMatch";

            header("Location: ../forgot/?s=$selector&v=$validator");
            exit();
        }

        $currentDate = date("U");

        $sqlQuery = "SELECT * FROM password_reset WHERE selector=? AND expire>=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $sqlError = "sqlError";

            header("Location: ../");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "ss", $selector, $currentDate);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if(!$row){
                $_SESSION['resetPasswordError'] = "expiredTokens";

                header("Location: ../forgot");
                exit();
            }
            else{
                $validator = $validator;
                $validatorCheck = password_verify($validator, $row['validator']);

                if(!$validatorCheck){
                    $_SESSION['resetPasswordError'] = "invalidTokens";

                    header("Location: ../forgot");
                    exit();
                }
                else{
                    $email = $row['email'];

                    $sqlQuery = "SELECT * FROM users WHERE email=?";
                    $stmt = mysqli_stmt_init($conn);

                    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                        $sqlError = "sqlError";

                        header("Location: ../");
                        exit();
                    }
                    else{
                        mysqli_stmt_bind_param($stmt, "s", $email);
                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt);
                        $row = mysqli_fetch_assoc($result);

                        if(!$row){
                            $_SESSION['resetPasswordError'] = "userDoNotExist";

                            header("Location: ../forgot");
                            exit();
                        }
                        else{
                            $sqlQuery = "UPDATE users SET pwd=? WHERE email=?";
                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                $sqlError = "sqlError";

                                header("Location: ../");
                                exit();
                            }
                            else{
                                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                                mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $email);
                                mysqli_stmt_execute($stmt);

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

                                    logoutFromAllDevices("password-reset");

                                    sendEmailPasswordChanged($email);

                                    $_SESSION['passwordResetSuccess'] = true;

                                    header("Location: ../login");
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    else{
        header("Location: ../");
        exit();
    }
?>