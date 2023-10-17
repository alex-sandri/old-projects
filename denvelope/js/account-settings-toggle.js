function general(anchor){
    hideAll();

    $("#general-settings").css("display", "block");
    $("#account-settings-a-gen").addClass("account-settings-a-selected");
    $("#account-settings-a-gen-mob").addClass("account-settings-a-selected");
    
    if(!anchor){
        hideMenuMob();
    }

    if($(window).width() > 1200){
        $("#account-settings-arrow-down-mob").removeClass("rotate");
    }
}

function security(anchor){
    hideAll();

    $("#security-settings").css("display", "block");
    $("#account-settings-a-sec").addClass("account-settings-a-selected");
    $("#account-settings-a-sec-mob").addClass("account-settings-a-selected");

    if(!anchor){
        hideMenuMob();
    }

    if($(window).width() > 1200){
        $("#account-settings-arrow-down-mob").removeClass("rotate");
    }
}

function advanced(anchor){
    hideAll();

    $("#advanced-settings").css("display", "block");
    $("#account-settings-a-adv").addClass("account-settings-a-selected");
    $("#account-settings-a-adv-mob").addClass("account-settings-a-selected");

    if(!anchor){
        hideMenuMob();
    }

    if($(window).width() > 1200){
        $("#account-settings-arrow-down-mob").removeClass("rotate");
    }
}

function info(anchor){
    hideAll();

    $("#info-settings").css("display", "block");
    $("#account-settings-a-inf").addClass("account-settings-a-selected");
    $("#account-settings-a-inf-mob").addClass("account-settings-a-selected");

    if(!anchor){
        hideMenuMob();
    }

    if($(window).width() > 1200){
        $("#account-settings-arrow-down-mob").removeClass("rotate");
    }
}

function plan(anchor){
    hideAll();

    $("#plan-settings").css("display", "block");
    $("#account-settings-a-pla").addClass("account-settings-a-selected");
    $("#account-settings-a-pla-mob").addClass("account-settings-a-selected");

    if(!anchor){
        hideMenuMob();
    }

    if($(window).width() > 1200){
        $("#account-settings-arrow-down-mob").removeClass("rotate");
    }
}

function privacy(anchor){
    hideAll();

    $("#privacy-settings").css("display", "block");
    $("#account-settings-a-pri").addClass("account-settings-a-selected");
    $("#account-settings-a-pri-mob").addClass("account-settings-a-selected");

    if(!anchor){
        hideMenuMob();
    }

    if($(window).width() > 1200){
        $("#account-settings-arrow-down-mob").removeClass("rotate");
    }
}

function support(anchor){
    hideAll();

    $("#support-settings").css("display", "block");
    $("#account-settings-a-sup").addClass("account-settings-a-selected");
    $("#account-settings-a-sup-mob").addClass("account-settings-a-selected");

    if(!anchor){
        hideMenuMob();
    }

    if($(window).width() > 1200){
        $("#account-settings-arrow-down-mob").removeClass("rotate");
    }
}

function hideMenuMob(){
    $("#account-settings-arrow-down-mob").toggleClass("rotate");
    $(".account-settings-div-mob").fadeTo(100, 0, "linear", function(){
        $(".account-settings-div-mob").css("display", "none");
    });
}

function hideAll(){
    $("#general-settings").css("display", "none");
    $("#account-settings-a-gen").removeClass("account-settings-a-selected");
    $("#account-settings-a-gen-mob").removeClass("account-settings-a-selected");

    $("#security-settings").css("display", "none");
    $("#account-settings-a-sec").removeClass("account-settings-a-selected");
    $("#account-settings-a-sec-mob").removeClass("account-settings-a-selected");

    $("#advanced-settings").css("display", "none");
    $("#account-settings-a-adv").removeClass("account-settings-a-selected");
    $("#account-settings-a-adv-mob").removeClass("account-settings-a-selected");

    $("#info-settings").css("display", "none");
    $("#account-settings-a-inf").removeClass("account-settings-a-selected");
    $("#account-settings-a-inf-mob").removeClass("account-settings-a-selected");

    $("#plan-settings").css("display", "none");
    $("#account-settings-a-pla").removeClass("account-settings-a-selected");
    $("#account-settings-a-pla-mob").removeClass("account-settings-a-selected");

    $("#privacy-settings").css("display", "none");
    $("#account-settings-a-pri").removeClass("account-settings-a-selected");
    $("#account-settings-a-pri-mob").removeClass("account-settings-a-selected");

    $("#support-settings").css("display", "none");
    $("#account-settings-a-sup").removeClass("account-settings-a-selected");
    $("#account-settings-a-sup-mob").removeClass("account-settings-a-selected");
}

$(window).resize(function(){
    if($(window).width() > 1200){
        $("#account-settings-arrow-down-mob").removeClass("rotate");
        $(".account-settings-div-mob").css("display", "none");
    }
});