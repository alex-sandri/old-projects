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
    <title><?php echo getTranslatedContent("signup_title"); ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <link rel="stylesheet" href="../css/signup-login-pages.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script async src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
    <script src="../js/pace.js"></script>
    <script src="../js/complete-forms-validation.php"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
    <?php
        if($isProduction){
            echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <script>
                    function onSubmitSignUp(token) {
                        document.getElementById("form-signup-form").submit();
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

    <div class="signup-form" id="signup-form">
        <h2 class="signup-form-h2"><?php echo getTranslatedContent("signup_title"); ?></h2>
        <br>
        <h6 class="or-login-form-h6"><?php echo getTranslatedContent("signup_or"); ?> <a href="../login" class="or-login-form-link"><?php echo getTranslatedContent("signup_or_login"); ?></a></h6>
        <br>
        <form action="../php/signup.php" method="post" id="form-signup-form" novalidate>
            <div class="input-div">
                <?php
                    if(isset($_SESSION['usernameError'])){
                        if($_SESSION['usernameError'] == "emptyField"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_username_empty"); echo'</p>';
                        }
                        else if($_SESSION['usernameError'] == "usernameTooShort"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_username_too_short"); echo'</p>';
                        }
                        else if($_SESSION['usernameError'] == "usernameTooLong"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_username_too_long"); echo'</p>';
                        }
                        else if($_SESSION['usernameError'] == "invalidUsername"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_username_invalid"); echo'</p>';
                        }
                        else if($_SESSION['usernameError'] == "alreadyTaken"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_username_already_taken"); echo'</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="username-error"></p>
                <div id="username-field-container" style="display: flex; border-radius: 5px;">
                    <input type="text" class="input" name="username" id="username" placeholder="<?php echo getTranslatedContent("signup_username"); ?>" value="<?php if(isset($_SESSION['usernameField']) && !isset($_SESSION['usernameError']) && !isset($_SESSION['signupSuccess'])) echo $_SESSION['usernameField'] ?>">
                    <div class="input-icon" id="username-icon-div">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
            <br>
            <div class="input-div">
                <?php
                    if(isset($_SESSION['emailError'])){
                        if($_SESSION['emailError'] == "emptyField"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_email_empty"); echo'</p>';
                        }
                        else if($_SESSION['emailError'] == "emailTooLong"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_email_too_long"); echo'</p>';
                        }
                        else if($_SESSION['emailError'] == "invalidEmail"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_email_invalid"); echo'</p>';
                        }
                        else if($_SESSION['emailError'] == "alreadyTaken"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_email_already_taken"); echo'</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="email-error"></p>
                <div id="email-field-container" style="display: flex; border-radius: 5px;">
                    <input type="email" class="input" name="email" id="email" placeholder="<?php echo getTranslatedContent("signup_email"); ?>" value="<?php if(isset($_SESSION['emailField']) && !isset($_SESSION['emailError']) && !isset($_SESSION['signupSuccess'])) echo $_SESSION['emailField'] ?>">
                    <div class="input-icon" id="email-icon-div">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
            </div>
            <br>
            <div class="input-div" style="font-size: 0px;">
                <?php
                    if(isset($_SESSION['passwordError'])){
                        if($_SESSION['passwordError'] == "emptyField"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_password_empty"); echo'</p>';
                        }
                        else if($_SESSION['passwordError'] == "passwordTooShort"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("signup_error_password_too_short"); echo'</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="signup-password-error"></p>
                <div id="signup-password-field-container" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
                    <div style="display: flex;">
                        <input type="password" class="input" name="password" id="password" placeholder="<?php echo getTranslatedContent("signup_password"); ?>" style="border-bottom-left-radius: 0px; border-bottom: 0px; padding-top: 16px; padding-bottom: 16px;">
                        <div class="input-icon" id="password-visibility-toggle" style="border-bottom-right-radius: 0px; cursor: pointer;">
                            <i class="fas fa-eye" id="pwd-visibility-eye"></i>
                            <i class="fas fa-eye-slash" id="pwd-visibility-eye-slash" style="display: none;"></i>
                        </div>
                    </div>
                    <div class="password-strength-meter" id="password-strength-meter">
                        <span></span>
                    </div>
                </div>
            </div>
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
            <?php
                if(!$betaHide){
                    echo '<br>
                        <p class="terms-privacy-statement">'; echo getTranslatedContent("signup_terms_privacy_statement"); echo'</p>
                    ';
                }
            ?>
            <br>
            <input type="hidden" name="signup-button-alt">
            <button type="submit" class="submit-button <?php if($isProduction) echo 'g-recaptcha'; ?>" name="signup-button" id="signup-button" <?php if($isProduction) echo 'data-sitekey="'; echo $reCAPTCHASiteKey; echo'" data-callback="onSubmitSignUp"'; ?>><?php echo getTranslatedContent("signup_signup_button"); ?></button>
        </form>
    </div>

    <?php
        unset($_SESSION['usernameError']);
        unset($_SESSION['emailError']);
        unset($_SESSION['passwordError']);
        unset($_SESSION['usernameField']);
        unset($_SESSION['emailField']);

        unset($_SESSION['recaptchaError']);
    ?>

    <?php
        if(isset($_COOKIE['consent'])){
            echo '<script>
                    $("#signup-form").css("margin-top", $("#header").outerHeight(true));
            
                    $(window).on("load", function(){
                        $("#signup-form").css("margin-top", $("#header").outerHeight(true));
                    });
            
                    $(window).resize(function(){
                        if($("#menu-mob").css("display") == "none"){
                            $("#signup-form").css("margin-top", $("#header").outerHeight(true));
                        }
                    });
                </script>
            ';
        }
    ?>
</body>
</html>