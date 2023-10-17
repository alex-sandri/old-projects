<?php
    if(isset($_SESSION['loginError']) && $_SESSION['loginError']){
        echo '<script>$("#body").attr("onload", "logIn()");</script>';
    }
    else if(isset($_SESSION['passwordResetError']) && $_SESSION['passwordResetError']){
        echo '<script>$("#body").attr("onload", "forgotPassword()");</script>';
    }
    else if(isset($_SESSION['signupSuccess']) && $_SESSION['signupSuccess'] == "success"){
        echo '<script>$("#body").attr("onload", "logIn()");</script>';
    }
    else if(isset($_SESSION['emailResent']) && $_SESSION['emailResent'] == true){
        echo '<script>$("#body").attr("onload", "logIn()");</script>';
    }
    else if(isset($_SESSION['resetPasswordEmailSent']) && $_SESSION['resetPasswordEmailSent'] == true){
        echo '<script>$("#body").attr("onload", "logIn()");</script>';
    }

    require("has-2fa.php");
?>

<div class="signup-login-form" id="signup-login-form">
    <div class="signup-form" id="signup-form">
        <h2 class="signup-form-h2"><?php echo getTranslatedContent("signup_title"); ?></h2>
        <br>
        <h6 class="or-login-form-h6"><?php echo getTranslatedContent("signup_or"); ?> <a onclick="logIn()" class="or-login-form-link"><?php echo getTranslatedContent("signup_or_login"); ?></a></h6>
        <br>
        <form action="php/signup.php" method="post" id="form-signup-form" novalidate>
            <div class="input-div">
                <?php
                    if(isset($_SESSION['usernameError'])){
                        if($_SESSION['usernameError'] == "emptyField"){
                            echo '<p class="error-input-field">Please enter a username</p>';
                        }
                        else if($_SESSION['usernameError'] == "usernameTooShort"){
                            echo '<p class="error-input-field">Username must be at least 4 characters long</p>';
                        }
                        else if($_SESSION['usernameError'] == "usernameTooLong"){
                            echo '<p class="error-input-field">Username length must be 15 characters or less</p>';
                        }
                        else if($_SESSION['usernameError'] == "invalidUsername"){
                            echo '<p class="error-input-field">Please enter a valid username<br>Please use only alphanumeric characters, dots, underscores and dashes</p>';
                        }
                        else if($_SESSION['usernameError'] == "alreadyTaken"){
                            echo '<p class="error-input-field">Username already taken</p>';
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
                            echo '<p class="error-input-field">Please enter an email address</p>';
                        }
                        else if($_SESSION['emailError'] == "emailTooLong"){
                            echo '<p class="error-input-field">Email address length must be under 255 characters</p>';
                        }
                        else if($_SESSION['emailError'] == "invalidEmail"){
                            echo '<p class="error-input-field">Please enter a valid email address</p>';
                        }
                        else if($_SESSION['emailError'] == "alreadyTaken"){
                            echo '<p class="error-input-field">Email address already registered</p>';
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
                            echo '<p class="error-input-field">Please enter a password</p>';
                        }
                        else if($_SESSION['passwordError'] == "passwordTooShort"){
                            echo '<p class="error-input-field">Password must be at least 8 characters long</p>';
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
                if(isset($betaHide) && !$betaHide){
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
    <div class="success-signup-box" id="success-signup-box">
        <p><?php echo getTranslatedContent("signup_message_box_success"); ?></p>
    </div>
    <div class="login-error-not-activated" id="login-error-not-activated">
        <form action="php/resend-confirm-email.php" method="post">
            <p><?php echo getTranslatedContent("login_message_box_not_activated"); ?></p>
        </form>
    </div>
    <div class="email-resent" id="email-resent">
        <p><?php echo getTranslatedContent("login_message_box_activation_email_resent"); ?></p>
    </div>
    <div class="reset-password-email-sent" id="reset-password-email-sent">
        <p><?php echo getTranslatedContent("forgot_password_message_box_email_sent"); ?></p>
    </div>
    <div class="login-form" id="login-form">
        <h2 class="login-form-h2"><?php echo getTranslatedContent("login_title"); ?></h2>
        <br>
        <h6 class="or-signup-form-h6"><?php echo getTranslatedContent("login_or"); ?> <a onclick="signUp()" class="or-signup-form-link"><?php echo getTranslatedContent("login_or_signup"); ?></a></h6>
        <br>
        <form action="php/login.php" method="post" id="form-login-form">
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
            <input type="hidden" name="from" value="home-page">
            <button type="submit" class="submit-button <?php if($isProduction) echo 'g-recaptcha'; ?>" name="login-button" id="login-button" style="margin-top: 1vh;" <?php if($isProduction) echo 'data-sitekey="'; echo $reCAPTCHASiteKey; echo'" data-callback="onSubmitLogIn"'; ?>><?php echo getTranslatedContent("login_login_button"); ?></button>
        </form>
        <br>
        <a onclick="forgotPassword()" class="forgot-password-link"><?php echo getTranslatedContent("login_forgot_password"); ?></a>    
    </div>
    <div class="forgot-password-form" id="forgot-password-form">
        <h2 class="forgot-password-form-h2"><?php echo getTranslatedContent("forgot_password_form_title"); ?></h2>
        <br>
        <h6 class="back-to-login-form-h6"><?php echo getTranslatedContent("forgot_password_back_to"); ?> <a onclick="logIn()" class="back-to-login-form-link"><?php echo getTranslatedContent("forgot_password_back_to_login"); ?></a></h6>
        <br>
        <form action="php/forgot-password.php" method="post">
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
</div>

<?php
    //unset error variables so if a user refreshes the page the errors are not being displayed
    unset($_SESSION['usernameError']);
    unset($_SESSION['emailError']);
    unset($_SESSION['passwordError']);
    unset($_SESSION['usernameEmailError']);
    unset($_SESSION['passwordLoginError']);
    unset($_SESSION['pwdResetEmailError']);
    //the same for input fields
    unset($_SESSION['usernameField']);
    unset($_SESSION['emailField']);
    unset($_SESSION['username-emailField']);
    //the password reset var
    unset($_SESSION['passwordResetError']);
    //unset the username check variables
    unset($_SESSION['usernameLength']);
    unset($_SESSION['usernameCheck']); 

    unset($_SESSION['recaptchaError']);
?>

<script src="<?php echo $urlPrefix; ?>js/complete-forms-validation.php"></script>

<script>
    function isNumber(e){
        if(!Number.isInteger(parseInt(String.fromCharCode(e.keyCode)))){
            return false;
        }
        else{
            return true;
        }
    }

    $(document).on("contextmenu",function(e){
        if(!$(e.target).is("input")){
            return false;
        }
    });

    $("#signup-form").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 2vw)");
    $("#login-form").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 2vw)");
    $("#forgot-password-form").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 2vw)");

    $(window).on("load", function(){
        $("#signup-form").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 2vw)");
        $("#login-form").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 2vw)");
        $("#forgot-password-form").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 2vw)");
    });

    $(window).resize(function(){
        if(document.getElementById("success-signup-box").style.display == "block"){
            $("#success-signup-box").css("margin-top", $("#header").outerHeight(true));
        }
        else if(document.getElementById("login-error-not-activated").style.display == "block"){
            $("#login-error-not-activated").css("margin-top", $("#header").outerHeight(true));
        }
        else if(document.getElementById("email-resent").style.display == "block"){
            $("#email-resent").css("margin-top", $("#header").outerHeight(true));
        }
        else if(document.getElementById("reset-password-email-sent").style.display == "block"){
            $("#reset-password-email-sent").css("margin-top", $("#header").outerHeight(true));
        }

        $("#signup-form").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 2vw)");
        
        if(document.getElementById("success-signup-box").style.display != "block" && document.getElementById("login-error-not-activated").style.display != "block" && document.getElementById("email-resent").style.display != "block" && document.getElementById("reset-password-email-sent").style.display != "block"){
            $("#login-form").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 2vw)");
        }
        else{
            $("#login-form").css("margin-top", "2vw");
        }

        $("#forgot-password-form").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 2vw)");
    });
</script>

<?php
    if(isset($_SESSION['signupSuccess']) && $_SESSION['signupSuccess'] == "success"){
        echo '<script>$(window).on("load", function(){$("#success-signup-box").css("display", "block");$("#success-signup-box").css("margin-top", $("#header").outerHeight(true));$("#login-form").css("margin-top", "2vw");});</script>';
    }
    else if(isset($_SESSION['loginError']) && $_SESSION['loginError'] === "accountNotActivated"){
        echo '<script>$(window).on("load", function(){$("#login-error-not-activated").css("display", "block");$("#login-error-not-activated").css("margin-top", $("#header").outerHeight(true));$("#login-form").css("margin-top", "2vw");});</script>';
    }
    else if(isset($_SESSION['emailResent']) && $_SESSION['emailResent'] == true){
        echo '<script>$(window).on("load", function(){$("#email-resent").css("display", "block");$("#email-resent").css("margin-top", $("#header").outerHeight(true));$("#login-form").css("margin-top", "2vw");});</script>';
    }
    else if(isset($_SESSION['resetPasswordEmailSent']) && $_SESSION['resetPasswordEmailSent'] == true){
        echo '<script>$(window).on("load", function(){$("#reset-password-email-sent").css("display", "block");$("#reset-password-email-sent").css("margin-top", $("#header").outerHeight(true));$("#login-form").css("margin-top", "2vw");});</script>';
    }

    unset($_SESSION['signupSuccess']);
    unset($_SESSION['loginError']);
    unset($_SESSION['emailResent']);
    unset($_SESSION['resetPasswordEmailSent']);
?>