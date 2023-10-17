<?php
    require_once("global-vars.php");
?>

<div class="cookie-banner" id="cookie-banner">
    <i class="fas fa-exclamation-circle"></i>
    <p><?php echo getTranslatedContent("cookie_banner_description"); ?> <a href="<?php echo $urlPrefix; ?>cookies"><?php echo getTranslatedContent("cookie_banner_learn_more"); ?></a></p>
    <i class="fas fa-times"></i>
</div>

<script>
    $("#cookie-banner i:last-child").click(function(){
        $("#cookie-banner").css("display", "none");
        $(".header, .signup-login-form").css("top", "0");
        $("#signup-form, #login-form, #forgot-password-form, #new-password-form, #contact-form").css("margin-top", $("#header").outerHeight(true));
    });

    $(".header, .signup-login-form").css("top", $("#cookie-banner").outerHeight(true) - $(window).scrollTop());

    $("#signup-form, #login-form, #forgot-password-form, #new-password-form, #contact-form").css("margin-top", $("#header").outerHeight(true) + $("#cookie-banner").outerHeight(true));

    $(window).resize(function(){
        if($("#cookie-banner").css("display") != "none"){
            $(".header, .signup-login-form").css("top", $("#cookie-banner").outerHeight(true) - $(window).scrollTop());
            $("#signup-form, #login-form, #forgot-password-form, #new-password-form, #contact-form").css("margin-top", $("#header").outerHeight(true) + $("#cookie-banner").outerHeight(true));
        }
    });

    $(window).on("load", function(){
        $(".header, .signup-login-form").css("top", $("#cookie-banner").outerHeight(true) - $(window).scrollTop());
        $("#signup-form, #login-form, #forgot-password-form, #new-password-form, #contact-form").css("margin-top", $("#header").outerHeight(true) + $("#cookie-banner").outerHeight(true));

        $.ajax({
            type: "POST",
            url: "<?php echo $urlPrefix; ?>php/set-consent-cookie.php",
            data: "",
            dataType: "JSON",
            success: function(r){

            },
            error: function(r){
                                
            }
        });
    })
</script>