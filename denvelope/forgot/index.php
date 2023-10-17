<?php
    session_start();

    if(isset($_SESSION['username'])){
        header("Location: ../");
        exit();
    }

    $enableIPLocation = true;

    if($enableIPLocation){
        if(!isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
            require("../php/translate-from-location.php");
        }
    }

    require("../php/dbh.php");
    require("../php/global-vars.php");
    require_once("../php/create-cookie.php");

    if(isset($_COOKIE['userSession'])){
        $sessionID = $_COOKIE['userSession'];

        if(!ctype_xdigit($sessionID)){
            $cookieError = "notValidCookie";

            require("../php/delete-cookie.php");
            deleteCookie("userSession");

            header("Location: ../");
            exit();
        }

        $sqlQuery = "SELECT * FROM sessions WHERE sessionID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $sqlError = "sqlError";

            header("Location: ../");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $sessionID);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if($row){
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];

                $sessionID = bin2hex(random_bytes(64));
                            
                $sqlQuery = "UPDATE sessions SET sessionID=? WHERE (username=? OR email=?) AND sessionID=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $sqlError = "sqlError";

                    header("Location: ../");
                    exit();
                }
                else{
                    mysqli_stmt_bind_param($stmt, "ssss", $sessionID, $_SESSION['username'], $_SESSION['email'], $_COOKIE['userSession']);
                    mysqli_stmt_execute($stmt);
                                
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];

                    createCookie("userSession", $sessionID);

                    mysqli_stmt_close($stmt);

                    header("Location: ../account");
                    exit();
                }
            }
            else{
                header("Location: ../php/logout.php");
            }
        }
    }
?>

<?php
    $betaHide = false;

    if($betaHide){
        header("Location: ../");
        exit();
    }
?>

<?php
    require("../lang/".$lang.".php");
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <?php
        if($isProduction){
            echo $googleAnalyticsTag;
        }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="<?php echo $HEADER_COLOR; ?>">
    <meta name="msapplication-navbutton-color" content="<?php echo $HEADER_COLOR; ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?php echo getTranslatedContent("forgot_password_title"); ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <link rel="stylesheet" href="../css/signup-login-pages.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="../js/pace.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/complete-forms-validation.php"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
</head>
<body>
    <?php
        if(!isset($_COOKIE['consent'])){
            require("../php/cookie-banner.php");
        }
    ?>
    
    <?php
        require("../php/basic-header-complete.php");
    ?>

    <div class="forgot-password-form" id="forgot-password-form">
        <h2 class="forgot-password-form-h2"><?php echo getTranslatedContent("forgot_password_form_title"); ?></h2>
        <br>
        <h6 class="back-to-login-form-h6"><?php echo getTranslatedContent("forgot_password_back_to"); ?> <a href="../login" class="back-to-login-form-link"><?php echo getTranslatedContent("forgot_password_back_to_login"); ?></a></h6>
        <br>
        <form action="../php/forgot-password.php" method="post">
            <div class="input-div">
                <?php
                    if(isset($_SESSION['pwdResetEmailError'])){
                        if($_SESSION['pwdResetEmailError'] == "emptyField"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("forgot_password_error_empty_field"); echo'</p>';
                        }
                        else if($_SESSION['pwdResetEmailError'] == "emailTooLong"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("forgot_password_error_email_too_long"); echo'</p>';
                        }
                        else if($_SESSION['pwdResetEmailError'] == "invalidEmail"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("forgot_password_error_invalid_email"); echo'</p>';
                        }
                        else if($_SESSION['pwdResetEmailError'] == "emailDoesNotExist"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("forgot_password_error_email_does_not_exist"); echo'</p>';
                        }
                    }
                    else if(isset($_SESSION['resetPasswordError'])){
                        if($_SESSION['resetPasswordError'] == "expiredTokens"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("forgot_password_error_expired_tokens"); echo'</p>';
                        }
                        else if($_SESSION['resetPasswordError'] == "invalidTokens"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("forgot_password_error_invalid_tokens"); echo'</p>';
                        }
                        else if($_SESSION['resetPasswordError'] == "userDoNotExist"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("forgot_password_error_user_do_not_exist"); echo'</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="password-reset-email-error"></p>
                <div id="pwdResetEmail-field-container" style="display: flex; border-radius: 5px;">
                    <input type="email" class="input" name="pwdResetEmail" id="pwdResetEmail" placeholder="<?php echo getTranslatedContent("forgot_password_email"); ?>">
                    <div class="input-icon" id="forgot-password-email-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
            </div>
            <br>
            <button type="submit" class="submit-button" name="password-reset-button" id="password-reset-button"><?php echo getTranslatedContent("forgot_password_submit"); ?></button>
        </form>
    </div>

    <?php
        unset($_SESSION['pwdResetEmailError']);
        unset($_SESSION['passwordResetError']);
        unset($_SESSION['resetPasswordError']);
    ?>

    <?php
        if(isset($_COOKIE['consent'])){
            echo '<script>
                    $("#forgot-password-form").css("margin-top", $("#header").outerHeight(true));

                    $(window).on("load", function(){
                        $("#forgot-password-form").css("margin-top", $("#header").outerHeight(true));
                    });
            
                    $(window).resize(function(){
                        if($("#menu-mob").css("display") == "none"){
                            $("#forgot-password-form").css("margin-top", $("#header").outerHeight(true));
                        }
                    });
                </script>
            ';
        }
    ?>
</body>
</html>