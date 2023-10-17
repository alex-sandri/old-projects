<?php
    session_start();

    $enableIPLocation = true;

    if($enableIPLocation){
        if(!isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
            require("../php/translate-from-location.php");
        }
    }

    require("../php/global-vars.php");

    if(isset($_COOKIE['userSession'])){
        require("../php/update-last-activity.php");

        updateLastActivity($_COOKIE['userSession']);
    }
?>

<?php
    require("../lang/".$lang.".php");
?>

<?php
    $betaHide = true;

    if($betaHide){
        header("Location: ../");
        exit();
    }
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
    <title><?php echo getTranslatedContent("pricing_title") ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/pace.js"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
</head>
<body>
    <?php
        if(!isset($_COOKIE['consent'])){
            require("../php/cookie-banner.php");
        }
    ?>

    <?php
        $betaDisableAccessHeader = false;

        require("../php/header.php");
    ?>

    <div class="pricing" id="pricing">
        <h2 class="signup-form-h2"><?php echo getTranslatedContent("pricing_title"); ?></h2>
        <div class="plan-container">
            <div class="plan-div">
                <p><?php echo getTranslatedContent("settings_plan_tier_free"); ?></p>
                <p><?php echo $FREE_TIER_PRICE.$currency; ?> / <?php echo getTranslatedContent("settings_plan_month"); ?></p>
                <p><?php echo $FREE_TIER_STORAGE; ?> <?php echo getTranslatedContent("settings_plan_storage"); ?></p>
            </div>
            <div class="plan-div">
                <p><?php echo getTranslatedContent("settings_plan_tier_personal"); ?></p>
                <p><?php echo $PERSONAL_TIER_PRICE.$currency; ?> / <?php echo getTranslatedContent("settings_plan_month"); ?></p>
                <p><?php echo $PERSONAL_TIER_STORAGE; ?> <?php echo getTranslatedContent("settings_plan_storage"); ?></p>
            </div>
            <div class="plan-div">
                <p><?php echo getTranslatedContent("settings_plan_tier_personal_plus"); ?></p>
                <p><?php echo $PERSONAL_PLUS_TIER_PRICE.$currency; ?> / <?php echo getTranslatedContent("settings_plan_month"); ?></p>
                <p><?php echo $PERSONAL_PLUS_TIER_STORAGE; ?> <?php echo getTranslatedContent("settings_plan_storage"); ?></p>
            </div>
            <div class="plan-div">
                <p><?php echo getTranslatedContent("settings_plan_tier_professional"); ?></p>
                <p><?php echo $PROFESSIONAL_TIER_PRICE.$currency; ?> / <?php echo getTranslatedContent("settings_plan_month"); ?></p>
                <p><?php echo $PROFESSIONAL_TIER_STORAGE; ?> <?php echo getTranslatedContent("settings_plan_storage"); ?></p>
            </div>
            <div class="plan-div">
                <p><?php echo getTranslatedContent("settings_plan_tier_professional_plus"); ?></p>
                <p><?php echo $PROFESSIONAL_PLUS_TIER_PRICE.$currency; ?> / <?php echo getTranslatedContent("settings_plan_month"); ?></p>
                <p><?php echo $PROFESSIONAL_PLUS_TIER_STORAGE; ?> <?php echo getTranslatedContent("settings_plan_storage"); ?></p>
            </div>
            <div class="plan-div">
                <p><?php echo getTranslatedContent("settings_plan_tier_enterprise"); ?></p>
                <p><?php echo $ENTERPRISE_TIER_PRICE.$currency; ?> / <?php echo getTranslatedContent("settings_plan_month"); ?></p>
                <p><?php echo $ENTERPRISE_TIER_STORAGE; ?> <?php echo getTranslatedContent("settings_plan_storage"); ?></p>
            </div>
            <a href="../../contact" class="need-more-container-link" style="width: 100%;">
                <div class="plan-div <?php ?>">
                    <p style="margin: 0;"><?php echo getTranslatedContent("settings_plan_need_more"); ?></p>
                </div>
            </a>
        </div>
        <br>
        <a href="../signup" class="submit-button pricing-button-to-signup"><?php echo getTranslatedContent("pricing_start_now_for_free"); ?></a>
        <br>
        <p><?php echo getTranslatedContent("pricing_note"); ?></p>
    </div>

    <style>
        .pricing{
            margin: 0 auto;
            width: 90%;
            color: var(--text-color);
            font-family: var(--font-family);
        }

        .plan-container{
            margin-top: 2vw;
        }
    </style>

    <script>
        $("#pricing").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");

        $(window).on("load", function(){
            $("#pricing").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");
        });

        $(window).resize(function(){
            if($("#menu-mob").css("display") == "none"){
                $("#pricing").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");
            }
        });
    </script>
</body>
</html>