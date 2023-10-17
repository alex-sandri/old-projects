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
    <title><?php echo getTranslatedContent("cookie_policy_title") ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/terms-privacy.css">
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

    <div id="cookie">
        <h2><?php echo getTranslatedContent("cookie_policy_title"); ?></h2>
        <h3><?php echo getTranslatedContent("cookie_policy_cookies_section_title"); ?></h3>
        <p><?php echo getTranslatedContent("cookie_policy_cookies_section_text"); ?></p>
        <ul>
            <li><p><?php echo getTranslatedContent("cookie_policy_cookies_section_list_login"); ?></p></li>
            <li><p><?php echo getTranslatedContent("cookie_policy_cookies_section_list_remember_settings"); ?></p></li>
            <li><p><?php echo getTranslatedContent("cookie_policy_cookies_section_list_keep_account_secure"); ?></p></li>
            <li><p><?php echo getTranslatedContent("cookie_policy_cookies_section_list_understand_use"); ?></p></li>
        </ul>
        <h3><?php echo getTranslatedContent("cookie_policy_opt_out_section_title"); ?></h3>
        <p><?php echo getTranslatedContent("cookie_policy_opt_out_section_text"); ?></p>
    </div>

    <script>
        $("#cookie").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");

        $(window).on("load", function(){
            $("#cookie").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");
        });

        $(window).resize(function(){
            if($("#menu-mob").css("display") == "none"){
                $("#cookie").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");
            }
        });
    </script>
</body>
</html>