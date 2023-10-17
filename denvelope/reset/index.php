<?php
    session_start();

    $enableIPLocation = true;

    if($enableIPLocation){
        if(!isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
            require("../php/translate-from-location.php");
        }
    }

    require("../php/global-vars.php");

    if(!isset($_GET['s']) || !isset($_GET['v'])){
        header("Location: ../");
        exit();
    }
?>

<?php
    if(isset($_COOKIE['userSession'])){
        require("../php/update-last-activity.php");

        updateLastActivity($_COOKIE['userSession']);
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
    <title><?php echo getTranslatedContent("reset_password_title"); ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <link rel="stylesheet" href="../css/signup-login-pages.css">
    <link rel="stylesheet" href="../css/footer.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="../js/pace.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/complete-forms-validation.php"></script>
    <script async src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
</head>
<body id="body">

    <?php
        if(!isset($_COOKIE['consent'])){
            require("../php/cookie-banner.php");
        }
    ?>

    <?php
        require("../php/basic-header-complete.php");
    ?>

    <?php
        if(isset($_GET['s'])){
            $selector = $_GET['s'];
        }
        if(isset($_GET['v'])){
            $validator = $_GET['v'];
        }

        if(empty($selector) || empty($validator)){
            header("Location: ../");
            exit();
        }
        else{
            if(ctype_xdigit($selector) && ctype_xdigit($validator)){
                ?>
                    <div class="new-password-form" id="new-password-form">
                        <h2 class="new-password-form-h2"><?php echo getTranslatedContent("reset_password_title"); ?></h2>
                        <br>
                        <form action="../php/reset-password.php" method="post">
                            <input type="hidden" class="input" name="selector" id="selector" value="<?php echo $selector; ?>">
                            <input type="hidden" class="input" name="validator" id="validator" value="<?php echo $validator; ?>">
                            <div class="input-div" style="font-size: 0px;">
                                <?php
                                    if(isset($_SESSION['resetPasswordError'])){
                                        if($_SESSION['resetPasswordError'] == "emptyNewPassword"){
                                            echo '<p class="error-input-field">'; echo getTranslatedContent("reset_password_error_empty_new_password"); echo'</p>';
                                        }
                                        else if($_SESSION['resetPasswordError'] == "passwordTooShort"){
                                            echo '<p class="error-input-field">'; echo getTranslatedContent("reset_password_error_password_too_short"); echo'</p>';
                                        }
                                    }
                                ?>
                                <p class="error-input-field" id="new-password-error"></p>
                                <div id="new-password-field-container" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
                                    <div style="display: flex;">
                                        <input type="password" class="input" name="new-password" id="new-password" placeholder="<?php echo getTranslatedContent("reset_password_new_password"); ?>" style="border-radius: 0; border-top-left-radius: 5px; border-bottom: 0; padding-top: 16px; padding-bottom: 16px;">
                                        <div class="input-icon" id="password-visibility-toggle-new" style="border-bottom-right-radius: 0px; cursor: pointer;">
                                            <i class="fas fa-eye" id="pwd-visibility-eye-new"></i>
                                            <i class="fas fa-eye-slash" id="pwd-visibility-eye-slash-new" style="display: none;"></i>
                                        </div>
                                    </div>
                                    <div class="password-strength-meter" id="password-strength-meter">
                                        <span></span>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="input-div" style="font-size: 0px;">
                                <?php
                                    if(isset($_SESSION['resetPasswordError'])){
                                        if($_SESSION['resetPasswordError'] == "emptyRepeatPassword"){
                                            echo '<p class="error-input-field">'; echo getTranslatedContent("reset_password_error_empty_repeat_password"); echo'</p>';
                                        }
                                        else if($_SESSION['resetPasswordError'] == "passwordsDoNotMatch"){
                                            echo '<p class="error-input-field">'; echo getTranslatedContent("reset_password_error_passwords_do_not_match"); echo'</p>';
                                        }
                                    }
                                ?>
                                <p class="error-input-field" id="repeat-new-password-error"></p>
                                <div id="repeat-new-password-field-container" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
                                    <div style="display: flex;">
                                        <input type="password" class="input" name="repeat-new-password" id="repeat-new-password" placeholder="<?php echo getTranslatedContent("reset_password_repeat_password"); ?>" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                        <div class="input-icon" id="password-visibility-toggle-repeat" style="cursor: pointer;">
                                            <i class="fas fa-eye" id="pwd-visibility-eye-repeat"></i>
                                            <i class="fas fa-eye-slash" id="pwd-visibility-eye-slash-repeat" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <button type="submit" class="submit-button" name="new-password-button" id="new-password-button"><?php echo getTranslatedContent("reset_password_submit"); ?></button>
                        </form>
                    </div>
                <?php
            }
            else{
                header("Location: ../");
                exit();
            }
        }
    ?>

    <?php
        unset($_SESSION['resetPasswordError']);
    ?>

    <?php
        if(isset($_COOKIE['consent'])){
            echo '<script>
                    $("#new-password-form").css("margin-top", $("#header").outerHeight(true));

                    $(window).on("load", function(){
                        $("#new-password-form").css("margin-top", $("#header").outerHeight(true));
                    });
            
                    $(window).resize(function(){
                        if($("#menu-mob").css("display") == "none"){
                            $("#new-password-form").css("margin-top", $("#header").outerHeight(true));
                        }
                    });
                </script>
            ';
        }
    ?>

</body>
</html>
