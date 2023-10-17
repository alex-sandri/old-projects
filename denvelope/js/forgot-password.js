function forgotPassword(){
    //signup header hidden and remove class (if an error occurs during password reset)
    document.getElementById("signup-form").style.display = "none";
    document.getElementById("signup-header").classList.remove("signup-header-clicked");
    //login header hidden and remove class
    document.getElementById("login-form").style.display = "none";
    document.getElementById("login-header").classList.remove("login-header-clicked");
    //display forgot password form
    document.getElementById("forgot-password-form").style.display = "block";
    //hide signup success box, login error box, email resent box, reset password email box and reset login form margin-top
    document.getElementById("success-signup-box").style.display = "none";
    document.getElementById("login-error-not-activated").style.display = "none";
    document.getElementById("email-resent").style.display = "none";
    document.getElementById("reset-password-email-sent").style.display = "none";
    
    $("#login-form").css("margin-top", $("#header").outerHeight(true) + (($("#login-form").outerWidth(true) - $("#login-form").outerWidth()) / 2));
}