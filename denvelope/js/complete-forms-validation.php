<?php
    header('Content-Type: application/javascript');

    require("../php/global-vars.php");

    require("../lang/".$lang.".php");
?>

$(document).ready(function(){

    var username = $("#username").val();
    var email = $("#email").val();
    var signupPassword = $("#signup-form #password").val();

    var usernameEmail = $("#username-email").val();
    var loginPassword = $("#login-password").val();
    var rememberMe = $("#remember-me-login").val();

    var forgotPasswordEmail = $("#pwdResetEmail").val();

    var newPassword = $("#new-password").val();
    var repeatNewPassword = $("#repeat-new-password").val();

    var patternUsername = /^[a-zA-Z0-9\.\-_]*$/;
    var validUsername = false;
    var patternEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var validEmail = false;
    var validPassword = false;

    var validPasswordLogin = false;

    var validForgotPwdEmail = false;

    var validNewPassword = false;
    var repeatNewPasswordMatch = false;

    var alreadyTakenUsername = false;

    var TwoFACode = "";
    var needs2FACode = false;
    var valid2FACode = false;
    var pattern2FA = /^\d+$/;

    checkEmptySignup();
    checkEmptyLogin();
    checkEmptyPwdReset();
    checkEmptyNewPassword();

    try{
        validUsername = checkUsername();
    }
    catch(err){
        //errors from using this from other pages other than signup
    }

    if(validUsername){
        checkExistingUsername();
    }

    try{
        validEmail = checkEmail(email);
    }
    catch(err){
        //errors from using this from other pages other than signup
    }

    if(validEmail){
        checkExistingEmail();
    }

    $("#username").on("keyup input", function(){
        username = $("#username").val();
        validUsername = checkUsername();
        checkEmptySignup();
        
        if(username.length >= 4 && username.length <= 15){
            checkExistingUsername();
        }

        if($("#username-error").html() != ""){
            errorMessageCheck("username");
        }
    });
    $("#email").on("keyup input" ,function(){
        email = $("#email").val();
        validEmail = checkEmail(email);
        checkEmptySignup();

        if(email.length > 0 && email.length <= 255){
            checkExistingEmail();
        }

        if($("#email-error").html() != ""){
            errorMessageCheck("email");
        }
    });
    $("#signup-form #password").on("keyup input", function(){
        signupPassword = $("#signup-form #password").val();
        validPassword = checkPasswordLength(signupPassword);
        checkEmptySignup();
        checkPasswordStrength(signupPassword);

        if($("#signup-password-error").html() != ""){
            errorMessageCheck("signupPassword");
        }
    });

    $("#username").focusout(function(){
        errorMessageCheck("username");
        $("#username-field-container").css("box-shadow", "none");
    });
    $("#email").focusout(function(){
        errorMessageCheck("email");
        $("#email-field-container").css("box-shadow", "none");
    });
    $("#signup-form #password").focusout(function(){
        errorMessageCheck("signupPassword");
        $("#signup-password-field-container").css("box-shadow", "none");
    });

    $("#username").focusin(function(){
        $("#username-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
    });
    $("#email").focusin(function(){
        $("#email-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
    });
    $("#signup-form #password").focusin(function(){
        $("#signup-password-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
    });

    $("#username-email").on("keyup input", function(){
        usernameEmail = $("#username-email").val();
        checkEmptyLogin();

        has2FA();

        if($("#username-email-error").html() != ""){
            errorMessageCheck("usernameEmail");
        }

        usernameEmailIconChange();
    });
    $("#login-password").on("keyup input", function(){
        loginPassword = $("#login-password").val();
        validPasswordLogin = checkPasswordLength(loginPassword);
        checkEmptyLogin();

        if($("#login-password-error").html() != ""){
            errorMessageCheck("loginPassword");
        }
    });

    $("#username-email").focusout(function(){
        errorMessageCheck("usernameEmail");
        $("#username-email-field-container").css("box-shadow", "none");
    });
    $("#login-password").focusout(function(){
        errorMessageCheck("loginPassword");
        $("#login-password-field-container").css("box-shadow", "none");
    });

    $("#username-email").focusin(function(){
        $("#username-email-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
    });
    $("#login-password").focusin(function(){
        $("#login-password-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
    });

    $("#pwdResetEmail").on("keyup input" ,function(){
        forgotPasswordEmail = $("#pwdResetEmail").val();
        validForgotPwdEmail = checkEmail(forgotPasswordEmail);
        checkEmptyPwdReset();

        if($("#password-reset-email-error").html() != ""){
            errorMessageCheck("forgotPasswordEmail");
        }
    });

    $("#pwdResetEmail").focusout(function(){
        errorMessageCheck("forgotPasswordEmail");
        $("#pwdResetEmail-field-container").css("box-shadow", "none");
    });

    $("#pwdResetEmail").focusin(function(){
        $("#pwdResetEmail-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
    });

    $("#new-password").on("keyup input", function(){
        newPassword = $("#new-password").val();
        validNewPassword = checkPasswordLength(newPassword);
        repeatNewPasswordMatch = newPassword == repeatNewPassword
        checkEmptyNewPassword();
        checkPasswordStrength(newPassword);

        if($("#new-password-error").html() != ""){
            errorMessageCheck("newPassword");
        }

        if(newPassword.length < 8){
            $("#repeat-new-password-error").html("");
            $("#repeat-new-password").css("background-color", "var(--input-bgcolor)");
            $("#repeat-new-password").css("color", "var(--text-color)");
            $("#repeat-new-password").removeClass("error-placeholder");
        }
    });
    $("#repeat-new-password").on("keyup input", function(){
        repeatNewPassword = $("#repeat-new-password").val();
        repeatNewPasswordMatch = newPassword == repeatNewPassword;
        checkEmptyNewPassword();

        if($("#repeat-new-password-error").html() != ""){
            errorMessageCheck("repeatNewPassword");
        }
    });

    $("#new-password").focusout(function(){
        errorMessageCheck("newPassword");
        $("#new-password-field-container").css("box-shadow", "none");
    });
    $("#repeat-new-password").focusout(function(){
        errorMessageCheck("repeatNewPassword");
        $("#repeat-new-password-field-container").css("box-shadow", "none");
    });

    $("#new-password").focusin(function(){
        $("#new-password-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
    });
    $("#repeat-new-password").focusin(function(){
        $("#repeat-new-password-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
    });

    $("#signup-button").click(function(e){
        if(!validSignupCondition()){
            e.preventDefault();

            errorMessageCheck("username");
            errorMessageCheck("email");
            errorMessageCheck("signupPassword");
        }
    });
    $("#login-button").click(function(e){
        if((usernameEmail == "" || loginPassword == "") || !validPasswordLogin || (needs2FACode && !valid2FACode)){
            e.preventDefault();

            errorMessageCheck("usernameEmail");
            errorMessageCheck("loginPassword");
            errorMessageCheck("2FACode");
        }
    });
    $("#password-reset-button").click(function(e){
        if(forgotPasswordEmail == "" || !validForgotPwdEmail){
            e.preventDefault();

            errorMessageCheck("forgotPasswordEmail");
        }
    });
    $("#new-password-button").click(function(e){
        if(newPassword == "" || repeatNewPassword == "" || !validNewPassword || !repeatNewPasswordMatch){
            e.preventDefault();

            errorMessageCheck("newPassword");
            errorMessageCheck("repeatNewPassword");
        }
    });

    $("#remember-me-login").click(function(){
        if($("#remember-me-login").is(":checked")){
            $("#remember-me-login").val("true");
        }
        else{
            $("#remember-me-login").val("false");
        }

        rememberMe = $("#remember-me-login").val()
    });

    $("#password-visibility-toggle").click(function(){
        if($("#pwd-visibility-eye").css("display") != "none"){
            $("#pwd-visibility-eye").css("display", "none");
            $("#pwd-visibility-eye-slash").css("display", "inline-block");

            $("#signup-form #password").attr("type", "text");
        }
        else{
            $("#pwd-visibility-eye-slash").css("display", "none");
            $("#pwd-visibility-eye").css("display", "inline-block");

            $("#signup-form #password").attr("type", "password");
        }
    });

    $("#password-visibility-toggle-login").click(function(){
        if($("#pwd-visibility-eye-login").css("display") != "none"){
            $("#pwd-visibility-eye-login").css("display", "none");
            $("#pwd-visibility-eye-slash-login").css("display", "inline-block");

            $("#login-password").attr("type", "text");
        }
        else{
            $("#pwd-visibility-eye-slash-login").css("display", "none");
            $("#pwd-visibility-eye-login").css("display", "inline-block");

            $("#login-password").attr("type", "password");
        }
    });

    $("#password-visibility-toggle-new").click(function(){
        if($("#pwd-visibility-eye-new").css("display") != "none"){
            $("#pwd-visibility-eye-new").css("display", "none");
            $("#pwd-visibility-eye-slash-new").css("display", "inline-block");

            $("#new-password").attr("type", "text");
        }
        else{
            $("#pwd-visibility-eye-slash-new").css("display", "none");
            $("#pwd-visibility-eye-new").css("display", "inline-block");

            $("#new-password").attr("type", "password");
        }
    });

    $("#password-visibility-toggle-repeat").click(function(){
        if($("#pwd-visibility-eye-repeat").css("display") != "none"){
            $("#pwd-visibility-eye-repeat").css("display", "none");
            $("#pwd-visibility-eye-slash-repeat").css("display", "inline-block");

            $("#repeat-new-password").attr("type", "text");
        }
        else{
            $("#pwd-visibility-eye-slash-repeat").css("display", "none");
            $("#pwd-visibility-eye-repeat").css("display", "inline-block");

            $("#repeat-new-password").attr("type", "password");
        }
    });

    function checkEmptySignup(){
        if(!validSignupCondition()){
            $("#signup-button").css("cursor", "not-allowed");
            $("#signup-button").css("opacity", "0.7");
            $("#signup-button").addClass("nohover");
        }
        else{
            $("#signup-button").css("cursor", "pointer");
            $("#signup-button").css("opacity", "1");
            $("#signup-button").removeClass("nohover");
        }
    }   

    function checkEmptyLogin(){
        if(usernameEmail == "" || loginPassword == "" || !validPasswordLogin || (needs2FACode && !valid2FACode)){
            $("#login-button").css("cursor", "not-allowed");
            $("#login-button").css("opacity", "0.7");
            $("#login-button").addClass("nohover");

            usernameEmail = $("#username-email").val();
            loginPassword = $("#login-password").val();
        }
        else{
            $("#login-button").css("cursor", "pointer");
            $("#login-button").css("opacity", "1");
            $("#login-button").removeClass("nohover");
        }
    }

    function checkEmptyPwdReset(){
        if(forgotPasswordEmail == "" || !validForgotPwdEmail){
            $("#password-reset-button").css("cursor", "not-allowed");
            $("#password-reset-button").css("opacity", "0.7");
            $("#password-reset-button").addClass("nohover");
        }
        else{
            $("#password-reset-button").css("cursor", "pointer");
            $("#password-reset-button").css("opacity", "1");
            $("#password-reset-button").removeClass("nohover");
        }
    }

    function checkEmptyNewPassword(){
        if(newPassword == "" || repeatNewPassword == "" || !validNewPassword || !repeatNewPasswordMatch){
            $("#new-password-button").css("cursor", "not-allowed");
            $("#new-password-button").css("opacity", "0.7");
            $("#new-password-button").addClass("nohover");
        }
        else{
            $("#new-password-button").css("cursor", "pointer");
            $("#new-password-button").css("opacity", "1");
            $("#new-password-button").removeClass("nohover");
        }
    }

    function checkUsername(){
        if(username.length >= 4 && username.length <= 255){
            if(patternUsername.test(username)){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    function checkEmail(email){
        if(email.length <= 255){
            if(patternEmail.test(email)){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    function checkPasswordLength(password){
        if(password.length >= 8){
            return true;
        }
        else{
            return false;
        }
    }

    function errorMessageCheck(inputField){
        switch(inputField){
            case "username":
                if(username.length <= 15){
                    if(username.length == 0){
                        $("#username-error").html("<?php echo getTranslatedContent("signup_error_username_empty"); ?>");
                        inputBackgroundError("username");
                    }
                    else if(username.length < 4){
                        $("#username-error").html("<?php echo getTranslatedContent("signup_error_username_too_short"); ?>");
                        inputBackgroundError("username");
                    }
                    else if(!patternUsername.test(username)){
                        $("#username-error").html("<?php echo getTranslatedContent("signup_error_username_invalid"); ?>");
                        inputBackgroundError("username");
                    }
                    else{
                        checkExistingUsername();
                    }
                }
                else{
                    $("#username-error").html("<?php echo getTranslatedContent("signup_error_username_too_long"); ?>");
                    inputBackgroundError("username");
                }
                break;
            case "email":
                if(email.length <= 255){
                    if(email.length == 0){
                        $("#email-error").html("<?php echo getTranslatedContent("signup_error_email_empty"); ?>");
                        inputBackgroundError("email");
                    }
                    else if(!patternEmail.test(email)){
                        $("#email-error").html("<?php echo getTranslatedContent("signup_error_email_invalid"); ?>");
                        inputBackgroundError("email");
                    }
                    else{
                        checkExistingEmail();
                    }
                }
                else{
                    $("#email-error").html("<?php echo getTranslatedContent("signup_error_email_too_long"); ?>");
                    inputBackgroundError("email");
                }
                break;
            case "signupPassword":
                if(signupPassword.length < 8){
                    if(signupPassword.length == 0){
                        $("#signup-password-error").html("<?php echo getTranslatedContent("signup_error_password_empty"); ?>");
                        inputBackgroundError("signup-form #password");
                    }
                    else{
                        $("#signup-password-error").html("<?php echo getTranslatedContent("signup_error_password_too_short"); ?>");
                        inputBackgroundError("signup-form #password");
                    }
                }
                else{
                    $("#signup-password-error").html("");
                    $("#signup-form #password").css("background-color", "var(--input-bgcolor)");
                    $("#signup-form #password").css("color", "var(--text-color)");
                    $("#signup-form #password").removeClass("error-placeholder");
                }
                break;
            case "usernameEmail":
                if(usernameEmail.length <= 255){
                    if(usernameEmail.length == 0){
                        $("#username-email-error").html("<?php echo getTranslatedContent("login_error_username_email_empty"); ?>");
                        inputBackgroundError("username-email");
                    }
                    else if(!patternUsername.test(usernameEmail) && !patternEmail.test(usernameEmail)){
                        $("#username-email-error").html("<?php echo getTranslatedContent("login_error_username_email_invalid"); ?>");
                        inputBackgroundError("username-email");
                    }
                    else{
                        $("#username-email-error").html("");
                        $("#username-email").css("background-color", "var(--input-bgcolor)");
                        $("#username-email").css("color", "var(--text-color)");
                        $("#username-email").removeClass("error-placeholder");
                    }
                }
                else{
                    $("#username-email-error").html("<?php echo getTranslatedContent("login_error_username_email_invalid"); ?>");
                    inputBackgroundError("username-email");
                }
                break;
            case "loginPassword":
                if(loginPassword.length < 8){
                    if(loginPassword.length == 0){
                        $("#login-password-error").html("<?php echo getTranslatedContent("login_error_password_empty"); ?>");
                        inputBackgroundError("login-password");
                    }
                    else{
                        $("#login-password-error").html("<?php echo getTranslatedContent("login_error_password_invalid"); ?>");
                        inputBackgroundError("login-password");
                    }
                }
                else{
                    $("#login-password-error").html("");
                    $("#login-password").css("background-color", "var(--input-bgcolor)");
                    $("#login-password").css("color", "var(--text-color)");
                    $("#login-password").removeClass("error-placeholder");
                }
                break;
            case "2FACode":
                if(TwoFACode.length <= 7){
                    if(TwoFACode.length == 0){
                        $("#2fa-code-error").html("<?php echo getTranslatedContent("login_error_2fa_code_empty"); ?>");
                        inputBackgroundError("2fa-code");
                    }
                    else if(!pattern2FA.test(TwoFACode)){
                        $("#2fa-code-error").html("<?php echo getTranslatedContent("login_error_2fa_code_invalid"); ?>");
                        inputBackgroundError("2fa-code");
                    }
                    else if(TwoFACode.length == 7){
                        $("#2fa-code-error").html("");
                        $("#2fa-code").css("background-color", "var(--input-bgcolor)");
                        $("#2fa-code").css("color", "var(--text-color)");
                        $("#2fa-code").removeClass("error-placeholder");
                    }
                    else{
                        $("#2fa-code-error").html("<?php echo getTranslatedContent("login_error_2fa_code_too_short"); ?>");
                        inputBackgroundError("2fa-code");
                    }
                }
                else{
                    $("#2fa-code-error").html("<?php echo getTranslatedContent("login_error_2fa_code_too_long"); ?>");
                    inputBackgroundError("2fa-code");
                }
                break;
            case "forgotPasswordEmail":
                if(forgotPasswordEmail.length <= 255){
                    if(forgotPasswordEmail.length == 0){
                        $("#password-reset-email-error").html("<?php echo getTranslatedContent("forgot_password_error_empty_field"); ?>");
                        inputBackgroundError("pwdResetEmail");
                    }
                    else if(!patternEmail.test(forgotPasswordEmail)){
                        $("#password-reset-email-error").html("<?php echo getTranslatedContent("forgot_password_error_invalid_email"); ?>");
                        inputBackgroundError("pwdResetEmail");
                    }
                    else{
                        $("#password-reset-email-error").html("");
                        $("#pwdResetEmail").css("background-color", "var(--input-bgcolor)");
                        $("#pwdResetEmail").css("color", "var(--text-color)");
                        $("#pwdResetEmail").removeClass("error-placeholder");
                    }
                }
                else{
                    $("#password-reset-email-error").html("<?php echo getTranslatedContent("forgot_password_error_email_too_long"); ?>");
                    inputBackgroundError("pwdResetEmail");
                }
                break;
            case "newPassword":
                if(newPassword.length < 8){
                    if(newPassword.length == 0){
                        $("#new-password-error").html("<?php echo getTranslatedContent("reset_password_error_empty_new_password"); ?>");
                        inputBackgroundError("new-password");
                    }
                    else{
                        $("#new-password-error").html("<?php echo getTranslatedContent("reset_password_error_password_too_short"); ?>");
                        inputBackgroundError("new-password");
                    }

                    $("#repeat-new-password-error").html("");
                    $("#repeat-new-password").css("background-color", "var(--input-bgcolor)");
                    $("#repeat-new-password").css("color", "var(--text-color)");
                    $("#repeat-new-password").removeClass("error-placeholder");
                }
                else{
                    $("#new-password-error").html("");
                    $("#new-password").css("background-color", "var(--input-bgcolor)");
                    $("#new-password").css("color", "var(--text-color)");
                    $("#new-password").removeClass("error-placeholder");
                }
                break;
            case "repeatNewPassword":
                if(repeatNewPassword != newPassword){
                    $("#repeat-new-password-error").html("<?php echo getTranslatedContent("reset_password_error_passwords_do_not_match"); ?>");
                    inputBackgroundError("repeat-new-password");
                }
                else{
                    $("#repeat-new-password-error").html("");
                    $("#repeat-new-password").css("background-color", "var(--input-bgcolor)");
                    $("#repeat-new-password").css("color", "var(--text-color)");
                    $("#repeat-new-password").removeClass("error-placeholder");
                }
                break;
            case "subject":
                if(subject.length < 100){
                    if(subject.length == 0){
                        $("#subject-error").html("<?php echo getTranslatedContent("contact_error_subject_empty"); ?>");
                        inputBackgroundError("subject");
                    }
                    else{
                        $("#subject-error").html("");
                        $("#subject").css("background-color", "var(--input-bgcolor)");
                        $("#subject").css("color", "var(--text-color)");
                        $("#subject").removeClass("error-placeholder");
                    }
                }
                else{
                    $("#subject-error").html("<?php echo getTranslatedContent("contact_error_subject_too_long"); ?>");
                    inputBackgroundError("subject");
                }
                break;
            case "message":
                if(message.length < 5000){
                    if(message.length == 0){
                        $("#message-error").html("<?php echo getTranslatedContent("contact_error_message_empty"); ?>");
                        inputBackgroundError("message");
                    }
                    else{
                        $("#message-error").html("");
                        $("#message").css("background-color", "var(--input-bgcolor)");
                        $("#message").css("color", "var(--text-color)");
                        $("#message").removeClass("error-placeholder");
                    }
                }
                else{
                    $("#message-error").html("<?php echo getTranslatedContent("contact_error_message_too_long"); ?>");
                    inputBackgroundError("message");
                }
                break;
            default:
                break;
        }
    }

    function checkPasswordStrength(password){
        
        var pwdStrength = zxcvbn(password).score;

        if(password != ""){
            switch(pwdStrength){
                case 0:
                    $("#password-strength-meter span").css("width", "12.5%");
                    $("#password-strength-meter span").css("background-color", "#69140E");
                    break;
                case 1:
                    $("#password-strength-meter span").css("width", "25%");
                    $("#password-strength-meter span").css("background-color", "#CC2936");
                    break;
                case 2:
                    $("#password-strength-meter span").css("width", "50%");
                    $("#password-strength-meter span").css("background-color", "#BC5D2E");
                    break;
                case 3:
                    $("#password-strength-meter span").css("width", "75%");
                    $("#password-strength-meter span").css("background-color", "#226F54");
                    break;
                case 4:
                    $("#password-strength-meter span").css("width", "90%");
                    $("#password-strength-meter span").css("background-color", "#04E824");
                    break;
                default:
                    break;
            }
        }
        else{
            $("#password-strength-meter span").css("width", "0%");
            $("#password-strength-meter span").css("background-color", "none");
        }
    }

    function checkExistingUsername(){
        $.ajax({
            type: "POST",
            url: "<?php echo $urlPrefix; ?>php/check-username.php",
            data: "username=" + username,
            dataType: "JSON",
            success: function(r){
                if(r[0]['usernameCheck'] == "available"){
                    $("#username-error").html("");
                    $("#username").css("background-color", "var(--input-bgcolor)");
                    $("#username").css("color", "var(--text-color)");
                    $("#username").removeClass("error-placeholder");
                    alreadyTakenUsername = false;
                    checkEmptySignup();
                }
                else if(r[0]['usernameCheck'] == "alreadyTaken"){
                    $("#username-error").html("<?php echo getTranslatedContent("signup_error_username_already_taken"); ?>");
                    inputBackgroundError("username");
                    alreadyTakenUsername = true;
                    checkEmptySignup();
                }
            },
            error: function(r){
                alreadyTakenUsername = false;
            }
        });
    }

    function checkExistingEmail(){
        $.ajax({
            type: "POST",
            url: "<?php echo $urlPrefix; ?>php/check-email.php",
            data: "email=" + email,
            dataType: "JSON",
            success: function(r){
                if(r[0]['emailCheck'] == "available"){
                    $("#email-error").html("");
                    $("#email").css("background-color", "var(--input-bgcolor)");
                    $("#email").css("color", "var(--text-color)");
                    $("#email").removeClass("error-placeholder");
                    alreadyTakenEmail = false;
                    checkEmptySignup();
                }
                else if(r[0]['emailCheck'] == "alreadyTaken"){
                    $("#email-error").html("<?php echo getTranslatedContent("signup_error_email_already_taken"); ?>");
                    inputBackgroundError("email");
                    alreadyTakenEmail = true;
                    checkEmptySignup();
                }
            },
            error: function(r){
                alreadyTakenEmail = false;
            }
        });
    }

    function inputBackgroundError(inputField){
        inputField = "#" + inputField;
        $(inputField).css("background-color", "var(--text-color)");
        $(inputField).css("color", "var(--header-color)");
        $(inputField).addClass("error-placeholder");
    }

    function usernameEmailIconChange(){
        if(patternUsername.test(usernameEmail)){
            $("#username-email-email-icon").css("display", "none");
            $("#username-email-wrong-icon").css("display", "none");
            $("#username-email-user-icon").css("display", "block");
        }
        else if(patternEmail.test(usernameEmail)){
            $("#username-email-user-icon").css("display", "none");
            $("#username-email-wrong-icon").css("display", "none");
            $("#username-email-email-icon").css("display", "block");
        }
        else{
            $("#username-email-user-icon").css("display", "none");
            $("#username-email-email-icon").css("display", "none");
            $("#username-email-wrong-icon").css("display", "block");
        }
    }

    function validSignupCondition(){
        if((username == "" || email == "" || signupPassword == "") || !validUsername || !validPassword || !validEmail || alreadyTakenUsername){
            return false;
        }
        else{
            return true;
        }
    }

    <!--CONTACT FORM-->
    var subject = $("#subject").val();
    var message = $("#message").val();

    try{
        $(".char-counter-contact-form-subject-div span").html($("#subject").val().length);
        $(".char-counter-contact-form-message-div span").html($("#message").val().length);

        checkEmptyContact();
    }
    catch (err){
        //errors from using this from other pages other than contact
    }

    $("#subject").on("keydown input", function(){
        $(".char-counter-contact-form-subject-div span").html($("#subject").val().length);
        subject = $("#subject").val();
        checkEmptyContact();

        if($("#subject-error").html() != ""){
            errorMessageCheck("subject");
        }
    });

    $("#message").on("keydown input", function(){
        $(".char-counter-contact-form-message-div span").html($("#message").val().length);
        message = $("#message").val();
        checkEmptyContact();

        if($("#message-error").html() != ""){
            errorMessageCheck("message");
        }
    });

    $("#subject").focusout(function(){
        errorMessageCheck("subject");
        $("#subject-field-container").css("box-shadow", "none");
    });
    $("#message").focusout(function(){
        errorMessageCheck("message");
        $("#message-field-container div:first-child").css("box-shadow", "none");
    });

    $("#subject").focusin(function(){
        $("#subject-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
    });
    $("#message").focusin(function(){
        $("#message-field-container div:first-child").css("box-shadow", "0px 0px 10px var(--text-color)");
    });

    $("#contact-button").click(function(e){
        if(subject.length == 0 || message.length == 0 || subject.length > 100 || message.length > 5000){
            e.preventDefault();

            errorMessageCheck("subject");
            errorMessageCheck("message");
        }
    });

    function checkEmptyContact(){
        if(subject == "" || message == "" || subject.length > 100 || message.length > 5000){
            $("#contact-button").css("cursor", "not-allowed");
            $("#contact-button").css("opacity", "0.7");
            $("#contact-button").addClass("nohover");
        }
        else{
            $("#contact-button").css("cursor", "pointer");
            $("#contact-button").css("opacity", "1");
            $("#contact-button").removeClass("nohover");
        }
    }

    <!--2FA CODE VALIDATION-->

    $("#2fa-code").on("keyup input", function(){
        TwoFACode = $("#2fa-code").val();

        if(pattern2FA.test(TwoFACode)){
            if(TwoFACode.length == 7){ <!--Authy 2FA Codes are 7 digits long (by default)-->
                valid2FACode = true;
            }
            else{
                valid2FACode = false;
            }
        }
        else{
            valid2FACode = false;
        }

        checkEmptyLogin();

        if($("#2fa-code-error").html() != ""){
            errorMessageCheck("2FACode");
        }
    });

    $("#2fa-code").focusin(function(){
        $("#2fa-code-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
    });
    $("#2fa-code").focusout(function(){
        errorMessageCheck("2FACode");
        $("#2fa-code-field-container").css("box-shadow", "none");
    });
    
    $("#send-code-via-sms").click(function(){
        $.ajax({
            type: "POST",
            url: "<?php echo $urlPrefix; ?>authy-functions/send-token-sms.php",
            data: "username-email-sms=" + usernameEmail,
            dataType: "JSON",
            success: function(r){
                if(r[0]['2FACodeSent'] == true){
                    $("#send-code-via-sms").css("display", "none");
                    $("#resend-code-via-sms").css("display", "block");
                    $("#resend-code-via-sms").css("cursor", "not-allowed");
                    $("#resend-code-via-sms").css("opacity", "0.7");
                    $("#resend-code-via-sms").addClass("nohover");
                    
                    startCountdown2FACode(20, $("#resend-code-via-sms").html());
                    
                    setTimeout(function(){
                        $("#resend-code-via-sms").css("cursor", "pointer");
                        $("#resend-code-via-sms").css("opacity", "1");
                        $("#resend-code-via-sms").removeClass("nohover");
                    }, 20000);
                }
                else if(r[0]['2FACodeSent'] == "not-needed"){
                    $("#2fa-code-div").css("display", "none");
                }
            },
            error: function(r){
                
            }
        });
    });

    $("#resend-code-via-sms").click(function(){
        if($(this).css("cursor") != "not-allowed"){
            $.ajax({
                type: "POST",
                url: "<?php echo $urlPrefix; ?>authy-functions/send-token-sms.php",
                data: "username-email-sms=" + usernameEmail,
                dataType: "JSON",
                success: function(r){
                    if(r[0]['2FACodeSent'] == true){
                        $("#resend-code-via-sms").css("cursor", "not-allowed");
                        $("#resend-code-via-sms").css("opacity", "0.7");
                        $("#resend-code-via-sms").addClass("nohover");
                        
                        startCountdown2FACode(20, $("#resend-code-via-sms").html());
                        
                        setTimeout(function(){
                            $("#resend-code-via-sms").css("cursor", "pointer");
                            $("#resend-code-via-sms").css("opacity", "1");
                            $("#resend-code-via-sms").removeClass("nohover");
                        }, 20000);
                    }
                    else if(r[0]['2FACodeSent'] == "not-needed"){
                        $("#2fa-code-div").css("display", "none");
                    }
                },
                error: function(r){
                    
                }
            });
        }
    });

    function has2FA(){
        $.ajax({
            type: "POST",
            url: "<?php echo $urlPrefix; ?>php/has-2fa.php",
            data: "username-email=" + usernameEmail,
            dataType: "JSON",
            success: function(r){
                if(r[0]['has2FA'] == true){
                    $("#2fa-code-div").css("display", "block");
                    needs2FACode = true;

                    checkEmptyLogin();
                }
                else{
                    $("#2fa-code-div").css("display", "none");
                    needs2FACode = false;

                    checkEmptyLogin();
                }
            },
            error: function(r){
                $("#2fa-code-div").css("display", "none");
                needs2FACode = false;

                checkEmptyLogin();
            }
        });
    }

    function startCountdown2FACode(seconds, htmlFieldText){
        $("#resend-code-via-sms").html(htmlFieldText + " (" + seconds + ")");

        var interval = setInterval(function(){
            $("#resend-code-via-sms").html(htmlFieldText);

            var secondsRemaining = --seconds;

            if(secondsRemaining > 0){
                $("#resend-code-via-sms").append(" (" + secondsRemaining + ")");
            }
            else{
                clearInterval(interval);
            }

        }, 1000);
    }
});