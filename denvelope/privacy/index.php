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
    <title><?php echo getTranslatedContent("privacy_policy_title") ?> - Denvelope</title>
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

    <div id="privacy">
        <h2>Privacy Policy</h2>
        <p>Your privacy is important to us. It is Denvelope's policy to respect your privacy regarding any information we may collect from you across our website, <a href="https://denvelope.com">https://denvelope.com</a>, and other sites we own and operate.</p>
        <p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we’re collecting it and how it will be used.</p>
        <p>We only retain collected information for as long as necessary to provide you with your requested service. What data we store, we’ll protect within commercially acceptable means to prevent loss and theft, as well as unauthorised access, disclosure, copying, use or modification.</p>
        <p>We don’t share any personally identifying information publicly or with third-parties, except when required to by law.</p>
        <p>Our website may link to external sites that are not operated by us. Please be aware that we have no control over the content and practices of these sites, and cannot accept responsibility or liability for their respective privacy policies.</p>
        <p>You are free to refuse our request for your personal information, with the understanding that we may be unable to provide you with some of your desired services.</p>
        <p>Your continued use of our website will be regarded as acceptance of our practices around privacy and personal information. If you have any questions about how we handle user data and personal information, feel free to contact us.</p>
        <p>This policy is effective as of 25 August 2019.</p>
    </div>

    <script>
        $("#privacy").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");

        $(window).on("load", function(){
            $("#privacy").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");
        });

        $(window).resize(function(){
            if($("#menu-mob").css("display") == "none"){
                $("#privacy").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");
            }
        });
    </script>
</body>
</html>