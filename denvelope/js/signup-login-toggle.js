var openedMenuMob;

function signUp() {
    //login header hidden and remove class
    document.getElementById("login-form").style.display = "none";
    document.getElementById("login-header").classList.remove("login-header-clicked");
    //hide eventual forgot password form
    document.getElementById("forgot-password-form").style.display = "none";
    //signup header visible
    document.getElementById("signup-login-form").style.display = "block";
    document.getElementById("signup-form").style.display = "block";
    document.getElementById("signup-header").classList.add("signup-header-clicked");

    //for mobile show arrow left icon and hide others
    document.getElementById("menu-bars-header").style.display = "none";
    document.getElementById("menu-times-header").style.display = "none";
    document.getElementById("menu-arrow-left-header").style.display = "block";

    if(openedMenuMob){
        document.getElementById("menu-mob").style.display = "none";
    }

    //hide signup success box, login error box, email resent box, reset password email box and reset login form margin-top
    document.getElementById("success-signup-box").style.display = "none";
    document.getElementById("login-error-not-activated").style.display = "none";
    document.getElementById("email-resent").style.display = "none";
    document.getElementById("reset-password-email-sent").style.display = "none";
    
    $("#login-form").css("margin-top", $("#header").outerHeight(true) + (($("#login-form").outerWidth(true) - $("#login-form").outerWidth()) / 2));
}

function logIn() {
    //signup header hidden and remove class
    document.getElementById("signup-form").style.display = "none";
    document.getElementById("signup-header").classList.remove("signup-header-clicked");
    //hide eventual forgot password form
    document.getElementById("forgot-password-form").style.display = "none";
    //login header visible
    document.getElementById("signup-login-form").style.display = "block";
    document.getElementById("login-form").style.display = "block";
    document.getElementById("login-header").classList.add("login-header-clicked");

    //for mobile show arrow left icon and hide others
    document.getElementById("menu-bars-header").style.display = "none";
    document.getElementById("menu-times-header").style.display = "none";
    document.getElementById("menu-arrow-left-header").style.display = "block";

    if(openedMenuMob){
        document.getElementById("menu-mob").style.display = "none";
    }
}

function closeForm() {
    try {
        document.getElementById("signup-login-form").style.display = "none";
        //signup header hidden and remove class
        document.getElementById("signup-form").style.display = "none";
        document.getElementById("signup-header").classList.remove("signup-header-clicked");
        //login header hidden and remove class
        document.getElementById("login-form").style.display = "none";
        document.getElementById("login-header").classList.remove("login-header-clicked");
    } catch (error) {
        
    }
}

function openMenuMob() {

    closeForm();

    document.getElementById("menu-mob").style.display = "block";
    document.getElementById("menu-bars-header").style.display = "none";
    document.getElementById("menu-times-header").style.display = "block";
    openedMenuMob = true;

    if($(window).width() <= 1200){
        $("#header").css("flex-direction", "column");
    }
}

function closeMenuMob() {

    closeForm();

    document.getElementById("menu-mob").style.display = "none";
    document.getElementById("menu-bars-header").style.display = "block";
    document.getElementById("menu-times-header").style.display = "none";
    openedMenuMob = false;

    if($(window).width() <= 1200){
        $("#header").css("flex-direction", "row");
    }
}

function backMenuMob() {
    document.getElementById("signup-form").style.display = "none";
    document.getElementById("login-form").style.display = "none";
    document.getElementById("forgot-password-form").style.display = "none";
    document.getElementById("menu-mob").style.display = "block";
    document.getElementById("menu-arrow-left-header").style.display = "none";
    document.getElementById("menu-times-header").style.display = "block";
}

//when logged in

function openMenuMobIn() {
    document.getElementById("menu-mob").style.display = "block";
    $("#menu-bars-header").css("display", "none");
    $("#menu-times-header").css("display", "block");

    if($(window).width() <= 1200){
        $("#header").css("flex-direction", "column");
    }

    $("#main-div-account-settings-mob").css("display", "none");
}

function closeMenuMobIn() {
    document.getElementById("menu-mob").style.display = "none";
    $("#menu-times-header").css("display", "none");
    $("#menu-bars-header").css("display", "block");

    if($(window).width() <= 1200){
        $("#header").css("flex-direction", "row");
    }

    $("#main-div-account-settings-mob").css("display", "block");
    $("#main-div-account").css("margin-top", $("#header").outerHeight(true));
}

$(window).resize(function(){
    if($(window).outerWidth() <= 1200){
        $("#header").css("flex-direction", "column");   
    }
    else{
        $("#header").css("flex-direction", "row");
    }
});