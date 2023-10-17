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
    require("../php/has-2fa.php");

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
    unset($_SESSION['redirectAfterLogin']);

    if(isset($_GET['ref'])){
        $ref = $_GET['ref'];

        if($ref != "account" && $ref != "settings" && $ref != "contact" && $ref != "supportcenter" && $ref != "home" && $ref != "adminpanel"){
            header("Location: ../login");
            exit();
        }

        if($ref == "settings"){
            $ref = "account/settings";
        }
        else if($ref == "home"){
            $ref = "";
        }
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
    <title><?php echo getTranslatedContent("login_title"); ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <link rel="stylesheet" href="../css/signup-login-pages.css">
    <link rel="stylesheet" href="../css/account.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="../js/pace.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/complete-forms-validation.php"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
    <?php
        if($isProduction){
            echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <script>
                    function onSubmitLogIn(token) {
                        document.getElementById("form-login-form").submit();
                    }
                </script>
            ';
        }
    ?>
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

    <div class="login-form" id="login-form">   
        <div class="login-error-not-activated" id="login-error-not-activated">
            <form action="../php/resend-confirm-email.php" method="post">
                <p><?php echo getTranslatedContent("login_message_box_not_activated"); ?></p>
            </form>
        </div>

        <?php
            if(isset($_GET['ref'])){
                echo '<div class="logout-redirect-please-login-div">
                        <h4>'; echo getTranslatedContent("login_please_login_to_continue"); echo'</h4>
                    </div>
                ';
            }
        ?>

        <h2 class="login-form-h2"><?php echo getTranslatedContent("login_title"); ?></h2>
        <br>
        <h6 class="or-signup-form-h6"><?php echo getTranslatedContent("login_or"); ?> <a href="../signup" class="or-signup-form-link"><?php echo getTranslatedContent("login_or_signup"); ?></a></h6>
        <br>
        <form action="../php/login.php<?php echo isset($ref) ? "?ref=".$ref : "" ?>" method="post" id="form-login-form">
            <div class="input-div">
                <?php
                    if(isset($_SESSION['usernameEmailError'])){
                        if($_SESSION['usernameEmailError'] == "emptyField"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_username_email_empty"); echo'</p>';
                        }
                        else if($_SESSION['usernameEmailError'] == "invalidUsernameEmail"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_username_email_invalid"); echo'</p>';
                        }
                        else if($_SESSION['usernameEmailError'] == "usernameEmailDoesNotExist"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_username_email_does_not_exist"); echo'</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="username-email-error"></p>
                <div id="username-email-field-container" style="display: flex; border-radius: 5px;">
                    <input type="text" class="input" name="username-email" id="username-email" placeholder="<?php echo getTranslatedContent("login_username_email"); ?>" value="<?php if(isset($_SESSION['username-emailField']) && !isset($_SESSION['usernameEmailError'])) echo $_SESSION['username-emailField'] ?>">
                    <div class="input-icon" id="username-email-icon-div">
                        <i class="fas fa-user" id="username-email-user-icon"></i>
                        <i class="fas fa-envelope" id="username-email-email-icon" style="display: none;"></i>
                        <i class="fas fa-times-circle" id="username-email-wrong-icon" style="display: none;"></i>
                    </div>
                </div>
            </div>
            <br>
            <div class="input-div">
                <?php
                    if(isset($_SESSION['passwordLoginError'])){
                        if($_SESSION['passwordLoginError'] == "emptyField"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_password_empty"); echo'</p>';
                        }
                        else if($_SESSION['passwordLoginError'] == "invalidPassword"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_password_invalid"); echo'</p>';
                        }
                        else if($_SESSION['passwordLoginError'] == "wrongPassword"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_password_not_correct"); echo'</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="login-password-error"></p>
                <div id="login-password-field-container" style="border-radius: 5px;">
                    <div style="display: flex;">
                        <input type="password" class="input" name="password" id="login-password" placeholder="<?php echo getTranslatedContent("login_password"); ?>">
                        <div class="input-icon" id="password-visibility-toggle-login" style="border-bottom-right-radius: 5px; cursor: pointer;">
                            <i class="fas fa-eye" id="pwd-visibility-eye-login"></i>
                            <i class="fas fa-eye-slash" id="pwd-visibility-eye-slash-login" style="display: none;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="input-div" id="2fa-code-div" style="display: <?php echo isset($_SESSION['2FACodeError']) || (isset($_SESSION['username-emailField']) && has2FAOnLogin($_SESSION['username-emailField'])) ? "block" : "none" ?>;">
                <?php
                    if(isset($_SESSION['2FACodeError'])){
                        if($_SESSION['2FACodeError'] == "emptyField"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_2fa_code_empty"); echo'</p>';
                        }
                        else if($_SESSION['2FACodeError'] == "tooLongCode"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_2fa_code_too_long"); echo'</p>';
                        }
                        else if($_SESSION['2FACodeError'] == "tooShortCode"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_2fa_code_too_short"); echo'</p>';
                        }
                        else if($_SESSION['2FACodeError'] == "invalidCode"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_2fa_code_invalid"); echo'</p>';
                        }
                        else if($_SESSION['2FACodeError'] == "wrongCode"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("login_error_2fa_code_wrong"); echo'</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="2fa-code-error"></p>
                <div id="2fa-code-field-container" style="border-radius: 5px;">
                    <div style="display: flex;">
                        <input type="text" class="input" onkeypress="return isNumber(event)" name="2fa-code" id="2fa-code" minlength="7" maxlength="7" placeholder="<?php echo getTranslatedContent("login_2fa_code"); ?>">
                        <div class="input-icon">
                            <i class="fas fa-key"></i>
                        </div>
                    </div>
                </div>
                <button type="button" id="send-code-via-sms" class="send-code-via-sms"><i class="fas fa-sms"></i> <?php echo getTranslatedContent("login_send_2fa_code_via_sms"); ?></button>
                <button type="button" id="resend-code-via-sms" class="resend-code-via-sms"><i class="fas fa-sync-alt"></i> <?php echo getTranslatedContent("login_resend_2fa_code_via_sms"); ?></button>
            </div>
            <br>
            <label for="remember-me-login" class="remember-me-container">
                <input type="checkbox" name="remember-me-login" id="remember-me-login" value="true" checked>
                <span class="checkmark"></span>   
                <?php echo getTranslatedContent("login_remember_me"); ?> 
            </label>
            <?php
                if($isProduction && isset($_SESSION['recaptchaError'])){
                    if($_SESSION['recaptchaError'] == "invalid"){
                        echo '<br>
                            <p class="error-input-field">Invalid CAPTCHA</p>
                        ';
                    }
                }

                if($isProduction){
                    echo '<br>
                        <p class="g-recaptcha-text">'; echo getTranslatedContent("signup_g_recaptcha_text"); echo'</p>
                    ';
                }
            ?>
            <br>
            <input type="hidden" name="login-button-alt">
            <input type="hidden" name="from" value="login-page">
            <button type="submit" class="submit-button <?php if($isProduction) echo 'g-recaptcha'; ?>" name="login-button" id="login-button" style="margin-top: 1vh;" <?php if($isProduction) echo 'data-sitekey="'; echo $reCAPTCHASiteKey; echo'" data-callback="onSubmitLogIn"'; ?>><?php echo getTranslatedContent("login_login_button"); ?></button>
        </form>
        <br>
        <a href="../forgot" class="forgot-password-link"><?php echo getTranslatedContent("login_forgot_password"); ?></a>    
    </div>

    <?php
        if(isset($_SESSION['loginError']) && $_SESSION['loginError'] === "accountNotActivated"){
            echo '<script>$(window).on("load", function(){$("#login-error-not-activated").css("display", "block");$("#login-error-not-activated").css("margin-bottom", "2vw");});</script>';
        }

        unset($_SESSION['loginError']);

        unset($_SESSION['passwordLoginError']);
        unset($_SESSION['usernameEmailError']);
        unset($_SESSION['username-emailField']);

        unset($_SESSION['recaptchaError']);
    ?>

    <script>
        function isNumber(e){
            if(!Number.isInteger(parseInt(String.fromCharCode(e.keyCode)))){
                return false;
            }
            else{
                return true;
            }
        }
    </script>

    <?php
        if(isset($_COOKIE['consent'])){
            echo '<script>
                    $("#login-form").css("margin-top", $("#header").outerHeight(true));

                    $(window).on("load", function(){
                        $("#login-form").css("margin-top", $("#header").outerHeight(true));
                    });
            
                    $(window).resize(function(){
                        if($("#menu-mob").css("display") == "none"){
                            $("#login-form").css("margin-top", $("#header").outerHeight(true));
                        }
                    });
                </script>
            ';
        }
    ?>
</body>
</html>