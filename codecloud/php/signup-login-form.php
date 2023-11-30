<?php
    if(isset($_SESSION['loginError']) && $_SESSION['loginError']){
        echo '<script>document.getElementById("body").onload = logIn;</script>';
    }
    else if(isset($_SESSION['passwordResetError']) && $_SESSION['passwordResetError']){
        echo '<script>document.getElementById("body").onload = forgotPassword;</script>';
    }
    else if(isset($_SESSION['signupSuccess']) && $_SESSION['signupSuccess'] == "success" && !isset($_SESSION['signupSuccessAjax'])){
        echo '<script>document.getElementById("body").onload = logIn;</script>';
    }
?>

<div class="signup-login-form" id="signup-login-form">
    <div class="signup-form" id="signup-form">
        <h2 class="signup-form-h2">Sign Up</h2>
        <br>
        <h6 class="or-login-form-h6">or <a onclick="logIn()" class="or-login-form-link">Log In</a></h6>
        <br>
        <form action="php/signup.php" method="post" novalidate>
            <div class="input-div">
                <?php
                    if(isset($_SESSION['usernameError'])){
                        if($_SESSION['usernameError'] == "emptyField"){
                            echo '<p class="error-input-field">Please enter a username</p>';
                        }
                        else if($_SESSION['usernameError'] == "usernameTooLong"){
                            echo '<p class="error-input-field">Username length must be under 255 characters</p>';
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
                    <input type="text" class="input" name="username" id="username" placeholder="Username" value="<?php if(isset($_SESSION['usernameField']) && !isset($_SESSION['usernameError']) && !isset($_SESSION['signupSuccess'])) echo $_SESSION['usernameField'] ?>">
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
                    <input type="email" class="input" name="email" id="email" placeholder="Email" value="<?php if(isset($_SESSION['emailField']) && !isset($_SESSION['emailError']) && !isset($_SESSION['signupSuccess'])) echo $_SESSION['emailField'] ?>">
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
                        <input type="password" class="input" name="password" id="password" placeholder="Password" style="border-bottom-left-radius: 0px; border-bottom: 0px; padding-top: 16px; padding-bottom: 16px;">
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
            <br>
            <button type="submit" class="submit-button" name="signup-button" id="signup-button">Sign Up <i class="fas fa-spinner" id="signup-spinner" style="display: none;"></i></button>
        </form>
    </div>
    <div class="success-signup-box" id="success-signup-box">
        <p>Congratulations! <br><br> Your account has been successfully created. <br><br> Just confirm your account from the email we sent you, and you're done</p>
    </div>
    <div class="login-form" id="login-form">
        <h2 class="login-form-h2">Log In</h2>
        <br>
        <h6 class="or-signup-form-h6">or <a onclick="signUp()" class="or-signup-form-link">Sign Up</a></h6>
        <br>
        <form action="php/login.php" method="post">
            <div class="input-div">
                <?php
                    if(isset($_SESSION['usernameEmailError'])){
                        if($_SESSION['usernameEmailError'] == "emptyField"){
                            echo '<p class="error-input-field">Please enter your username or email</p>';
                        }
                        else if($_SESSION['usernameEmailError'] == "invalidUsernameEmail"){
                            echo '<p class="error-input-field">Username or email not valid</p>';
                        }
                        else if($_SESSION['usernameEmailError'] == "usernameEmailDoesNotExist"){
                            echo '<p class="error-input-field">Username or email does not exists</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="username-email-error"></p>
                <div id="username-email-field-container" style="display: flex; border-radius: 5px;">
                    <input type="text" class="input" name="username-email" id="username-email" placeholder="Username / Email" value="<?php if(isset($_SESSION['username-emailField'])) echo $_SESSION['username-emailField'] ?>">
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
                            echo '<p class="error-input-field">Please enter your password</p>';
                        }
                        else if($_SESSION['passwordLoginError'] == "invalidPassword"){
                            echo '<p class="error-input-field">Password not valid</p>';
                        }
                        else if($_SESSION['passwordLoginError'] == "wrongPassword"){
                            echo '<p class="error-input-field">Password not correct</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="login-password-error"></p>
                <div id="login-password-field-container" style="border-radius: 5px;">
                    <div style="display: flex;">
                        <input type="password" class="input" name="password" id="password" placeholder="Password">
                        <div class="input-icon" id="password-visibility-toggle-login" style="border-bottom-right-radius: 5px; cursor: pointer;">
                            <i class="fas fa-eye" id="pwd-visibility-eye-login"></i>
                            <i class="fas fa-eye-slash" id="pwd-visibility-eye-slash-login" style="display: none;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <label for="remember-me-login" class="remember-me-container">
                <input type="checkbox" name="remember-me-login" id="remember-me-login" value="true" checked>
                <span class="checkmark"></span>   
                Remember Me 
            </label>
            <br>
            <button type="submit" class="submit-button" name="login-button" id="login-button" style="margin-top: 1vh;">Log In</button>
        </form>
        <br>
        <a onclick="forgotPassword()" class="forgot-password-link">Forgot your password?</a>    
    </div>
    <div class="forgot-password-form" id="forgot-password-form">
        <h2 class="forgot-password-form-h2">Password Reset</h2>
        <br>
        <h6 class="back-to-login-form-h6">back to <a onclick="logIn()" class="back-to-login-form-link">Log In</a></h6>
        <br>
        <form action="php/forgot-password.php" method="post">
            <div class="input-div">
                <?php
                    if(isset($_SESSION['pwdResetEmailError'])){
                        if($_SESSION['pwdResetEmailError'] == "emptyField"){
                            echo '<p class="error-input-field">Please enter your email</p>';
                        }
                        else if($_SESSION['pwdResetEmailError'] == "emailTooLong"){
                            echo '<p class="error-input-field">Email address length must be under 255 characters</p>';
                        }
                        else if($_SESSION['pwdResetEmailError'] == "invalidEmail"){
                            echo '<p class="error-input-field">Please enter a valid email address</p>';
                        }
                        else if($_SESSION['pwdResetEmailError'] == "emailDoesNotExist"){
                            echo '<p class="error-input-field">There is no user registered with this email</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="password-reset-email-error"></p>
                <div id="pwdResetEmail-field-container" style="display: flex; border-radius: 5px;">
                    <input type="email" class="input" name="pwdResetEmail" id="pwdResetEmail" placeholder="Email">
                    <div class="input-icon" id="forgot-password-email-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
            </div>
            <br>
            <button type="submit" class="submit-button" name="password-reset-button" id="password-reset-button">Submit</button>
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
    //the same for login, password reset and signup success error var
    unset($_SESSION['loginError']);
    unset($_SESSION['passwordResetError']);
    unset($_SESSION['signupSuccess']);
    //unset the username check variables
    unset($_SESSION['usernameLength']);
    unset($_SESSION['usernameCheck']); 
?>

<script>
    $(document).on("contextmenu",function(e){
        if(!$(e.target).is("input")){
            return false;
        }
    });

    $(document).ready(function(){

        var username = $("#username").val();
        var email = $("#email").val();
        var signupPassword = $("#signup-form #password").val();

        var usernameEmail = $("#username-email").val();
        var loginPassword = $("#login-form #password").val();
        var rememberMe = $("#remember-me-login").val();

        var forgotPasswordEmail = $("#pwdResetEmail").val();

        var patternUsername = /^[a-zA-Z0-9\.\-_ ]*$/;
        var validUsername = false;
        var patternEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        var validEmail = false;
        var validPassword = false;

        var validPasswordLogin = false;

        var validForgotPwdEmail = false;

        var signupSuccess = false;
        var loginSuccess = true;

        var alreadyTakenUsername = false;

        checkEmptySignup();
        checkEmptyLogin();
        checkEmptyPwdReset();
        validUsername = checkUsername();

        if(validUsername){
            checkExistingUsername();
        }

        validEmail = checkEmail(email);

        $("#username").keyup(function(){
            username = $("#username").val();
            validUsername = checkUsername();
            checkEmptySignup();
            
            if(username.length >= 4 && username.length <= 255){
                checkExistingUsername();
            }

            if($("#username-error").html() != ""){
                errorMessageCheck("username");
            }
        });
        $("#email").keyup(function(){
            email = $("#email").val();
            validEmail = checkEmail(email);
            checkEmptySignup();

            if($("#email-error").html() != ""){
                errorMessageCheck("email");
            }
        });
        $("#signup-form #password").keyup(function(){
            signupPassword = $("#signup-form #password").val();
            validPassword = checkPasswordLength(signupPassword);
            checkEmptySignup();
            checkPasswordStrength();

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

        $("#username-email").keyup(function(){
            usernameEmail = $("#username-email").val();
            checkEmptyLogin();

            if($("#username-email-error").html() != ""){
                errorMessageCheck("usernameEmail");
            }

            usernameEmailIconChange();
        });
        $("#login-form #password").keyup(function(){
            loginPassword = $("#login-form #password").val();
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
        $("#login-form #password").focusout(function(){
            errorMessageCheck("loginPassword");
            $("#login-password-field-container").css("box-shadow", "none");
        });

        $("#username-email").focusin(function(){
            $("#username-email-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
        });
        $("#login-form #password").focusin(function(){
            $("#login-password-field-container").css("box-shadow", "0px 0px 10px var(--text-color)");
        });

        $("#pwdResetEmail").keyup(function(){
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

        $("#signup-button").click(function(e){
            if(!validSignupCondition()){
                e.preventDefault();

                errorMessageCheck("username");
                errorMessageCheck("email");
                errorMessageCheck("signupPassword");
            }
            else{
                e.preventDefault();

                var formData = "username=" + username + "&email=" + email + "&password=" + signupPassword;

                $("#username").val("");
                $("#email").val("");
                $("#signup-form #password").val("");

                $("#signup-spinner").css("display", "inline-block");
                $("#signup-spinner").addClass("spinner-spin");

                $.ajax({
                    type: "POST",
                    url: "php/signup.php",
                    data: formData,
                    dataType: "JSON",
                    success: function(r){
                        if(r[0]['signupSuccess'] == "success"){
                            logIn();
                            document.getElementById("success-signup-box").style.display = "block";
                            $("#success-signup-box").css("margin-top", $("#header").outerHeight(true));
                            $('#login-form').css('margin-top', '2vw');
                            signupSuccess = true;

                            $("#signup-spinner").css("display", "none");
                            
                            checkEmptySignup();
                        }
                    },
                    error: function(r){
                        signupSuccess = false;

                        checkEmptySignup();
                    }
                });
            }
        });
        $("#login-button").click(function(e){
            if((usernameEmail == "" || loginPassword == "") || !validPasswordLogin){
                e.preventDefault();

                errorMessageCheck("usernameEmail");
                errorMessageCheck("loginPassword");
            }
            else{
                e.preventDefault();

                var formData = "username-email=" + usernameEmail + "&password=" + loginPassword + "&remember-me-login=" + rememberMe;

                $("#username-email").val("");
                $("#login-form #password").val("");

                $.ajax({
                    type: "POST",
                    url: "php/login.php",
                    data: formData,
                    dataType: "JSON",
                    success: function(r){
                        if(r[0]['loginSuccess'] == "success"){
                            $("#body").load("account");
                            history.pushState(null, "code.cloud -  Your Account", "/codecloud/account/");
                            loginSuccess = true;
                        }

                        checkEmptyLogin();
                    },
                    error: function(r){
                        loginSuccess = false;

                        checkEmptyLogin();
                    }
                })
            }
        });
        $("#password-reset-button").click(function(e){
            if(forgotPasswordEmail == "" || !validForgotPwdEmail){
                e.preventDefault();

                errorMessageCheck("forgotPasswordEmail");
            }
            else{
                e.preventDefault();

                var formData = "pwdResetEmail=" + forgotPasswordEmail;

                $("#pwdResetEmail").val("");

                $.ajax({
                    type: "POST",
                    url: "php/forgot-password.php",
                    data: formData,
                    dataType: "JSON",
                    success: function(r){

                    },
                    error: function(r){

                    }
                })
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

                $("#login-form #password").attr("type", "text");
            }
            else{
                $("#pwd-visibility-eye-slash-login").css("display", "none");
                $("#pwd-visibility-eye-login").css("display", "inline-block");

                $("#login-form #password").attr("type", "password");
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
            if(usernameEmail == "" || loginPassword == "" || !validPasswordLogin || !loginSuccess){
                $("#login-button").css("cursor", "not-allowed");
                $("#login-button").css("opacity", "0.7");
                $("#login-button").addClass("nohover");

                usernameEmail = $("#username-email").val();
                loginPassword = $("#login-form #password").val();
                
                loginSuccess = true;
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
                    if(username.length <= 255){
                        if(username.length == 0){
                            $("#username-error").html("Please enter a username");
                            inputBackgroundError("username");
                        }
                        else if(username.length < 4){
                            $("#username-error").html("Username must be at least 4 characters long");
                            inputBackgroundError("username");
                        }
                        else if(!patternUsername.test(username)){
                            $("#username-error").html("Please enter a valid username<br>Please use only alphanumeric characters, dots, underscores and dashes");
                            inputBackgroundError("username");
                        }
                        else{
                            checkExistingUsername();
                        }
                    }
                    else{
                        $("#username-error").html("Username length must be under 255 characters");
                        inputBackgroundError("username");
                    }
                    break;
                case "email":
                    if(email.length <= 255){
                        if(email.length == 0){
                            $("#email-error").html("Please enter an email address");
                            inputBackgroundError("email");
                        }
                        else if(!patternEmail.test(email)){
                            $("#email-error").html("Please enter a valid email address");
                            inputBackgroundError("email");
                        }
                        else{
                            $("#email-error").html("");
                            $("#email").css("background-color", "var(--input-bgcolor)");
                            $("#email").css("color", "var(--text-color)");
                            $("#email").removeClass("error-placeholder");
                        }
                    }
                    else{
                        $("#email-error").html("Email address length must be under 255 characters");
                        inputBackgroundError("email");
                    }
                    break;
                case "signupPassword":
                    if(signupPassword.length < 8){
                        if(signupPassword.length == 0){
                            $("#signup-password-error").html("Please enter a password");
                            inputBackgroundError("signup-form #password");
                        }
                        else{
                            $("#signup-password-error").html("Password must be at least 8 characters long");
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
                            $("#username-email-error").html("Please enter your username or email");
                            inputBackgroundError("username-email");
                        }
                        else if(!patternUsername.test(usernameEmail) && !patternEmail.test(usernameEmail)){
                            $("#username-email-error").html("Username or email not valid");
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
                        $("#username-email-error").html("Username or email not valid");
                        inputBackgroundError("username-email");
                    }
                    break;
                case "loginPassword":
                    if(loginPassword.length < 8){
                        if(loginPassword.length == 0){
                            $("#login-password-error").html("Please enter your password");
                            inputBackgroundError("login-form #password");
                        }
                        else{
                            $("#login-password-error").html("Password not valid");
                            inputBackgroundError("login-form #password");
                        }
                    }
                    else{
                        $("#login-password-error").html("");
                        $("#login-form #password").css("background-color", "var(--input-bgcolor)");
                        $("#login-form #password").css("color", "var(--text-color)");
                        $("#login-form #password").removeClass("error-placeholder");
                    }
                    break;
                case "forgotPasswordEmail":
                    if(forgotPasswordEmail.length <= 255){
                        if(forgotPasswordEmail.length == 0){
                            $("#password-reset-email-error").html("Please enter your email");
                            inputBackgroundError("pwdResetEmail");
                        }
                        else if(!patternEmail.test(forgotPasswordEmail)){
                            $("#password-reset-email-error").html("Please enter a valid email address");
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
                        $("#password-reset-email-error").html("Email address length must be under 255 characters");
                        inputBackgroundError("pwdResetEmail");
                    }
                    break;
                default:
                    break;
            }
        }

        function checkPasswordStrength(){
            
            var pwdStrength = zxcvbn(signupPassword).score;

            if(signupPassword != ""){
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
                url: "php/check-username.php",
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
                        $("#username-error").html("This username has already been taken");
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

        function inputBackgroundError(inputField){
            inputField = "#" + inputField;
            $(inputField).css("background-color", "var(--text-color)");
            $(inputField).css("color", "var(--header-color)");
            $(inputField).addClass("error-placeholder");
        }

        function usernameEmailIconChange(){
            if(patternUsername.test(usernameEmail)){
                $("#username-email-email-icon").css("display", "none");
                $("#username-email-user-icon").css("display", "block");
            }
            else if(patternEmail.test(usernameEmail)){
                $("#username-email-user-icon").css("display", "none");
                $("#username-email-email-icon").css("display", "block");
            }
            else{

            }
        }

        function validSignupCondition(){
            if((username == "" || email == "" || signupPassword == "") || !validUsername || !validPassword || !validEmail || signupSuccess || alreadyTakenUsername){
                return false;
            }
            else{
                return true;
            }
        }
    });

    $("#signup-form").css("margin-top", $("#header").outerHeight(true) + (($("#signup-form").outerWidth(true) - $("#signup-form").outerWidth()) / 2));
    $("#login-form").css("margin-top", $("#header").outerHeight(true) + (($("#login-form").outerWidth(true) - $("#login-form").outerWidth()) / 2));
    $("#forgot-password-form").css("margin-top", $("#header").outerHeight(true) + (($("#forgot-password-form").outerWidth(true) - $("#forgot-password-form").outerWidth()) / 2));

    $(window).on("load", function(){
        $("#signup-form").css("margin-top", $("#header").outerHeight(true) + (($("#signup-form").outerWidth(true) - $("#signup-form").outerWidth()) / 2));
        $("#login-form").css("margin-top", $("#header").outerHeight(true) + (($("#login-form").outerWidth(true) - $("#login-form").outerWidth()) / 2));
        $("#forgot-password-form").css("margin-top", $("#header").outerHeight(true) + (($("#forgot-password-form").outerWidth(true) - $("#forgot-password-form").outerWidth()) / 2));
    });

    $(window).resize(function(){
        if(document.getElementById("success-signup-box").style.display == "block"){
            $("#success-signup-box").css("margin-top", $("#header").outerHeight(true));
        }

        $("#signup-form").css("margin-top", $("#header").outerHeight(true) + (($("#signup-form").outerWidth(true) - $("#signup-form").outerWidth()) / 2));
        
        if(document.getElementById("success-signup-box").style.display != "block"){
            $("#login-form").css("margin-top", $("#header").outerHeight(true) + (($("#login-form").outerWidth(true) - $("#login-form").outerWidth()) / 2));
        }
        else{
            $("#login-form").css("margin-top", ($("#login-form").outerWidth(true) - $("#login-form").outerWidth()) / 2);
        }

        $("#forgot-password-form").css("margin-top", $("#header").outerHeight(true) + (($("#forgot-password-form").outerWidth(true) - $("#forgot-password-form").outerWidth()) / 2));
    });
</script>