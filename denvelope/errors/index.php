<?php
    if(isset($_GET['error'])){
        require("../php/add-error.php");

        $errorCode = $_GET['error'];

        if($errorCode != "404" && $errorCode != "403" && $errorCode != "500"){
            header("Location: ../");
            exit();
        }

        switch ($errorCode) {
            case '404':
                $errorMsg = "Not Found";
                break;
            case '403':
                $errorMsg = "Forbidden";
                break;
            case '500':
                $errorMsg = "Internal Server Error";
                break;
            default:
                $errorMsg = "";
                break;
        }

        addError($_GET['error']);
    }
    else{
        header("Location: ../");
        exit();
    }
?>

<?php
    if(isset($_COOKIE['userSession'])){
        require("../php/update-last-activity.php");

        updateLastActivity($_COOKIE['userSession']);
    }
?>

<?php
    $enableIPLocation = true;

    if($enableIPLocation){
        if(!isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
            require("../php/translate-from-location.php");
        }
    }

    require("../php/global-vars.php");
?>

<?php
    $betaHide = false;
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
    <base href="<?php echo $urlPrefix; ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="<?php echo $HEADER_COLOR; ?>">
    <meta name="msapplication-navbutton-color" content="<?php echo $HEADER_COLOR; ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?php echo $errorCode." - Denvelope" ?></title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/errors.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="js/pace.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
</head>
<body>
    <?php
        if(!isset($_COOKIE['consent'])){
            require("../php/cookie-banner.php");
        }
    ?>

    <h1><?php echo $errorCode ?></h1>
    <p><?php echo $errorMsg ?></p>

    <?php
        if($errorCode == "500"){
            require("../php/send-email-ses.php");
            require_once("../php/get-browser.php");
            require_once("../php/get-os.php");
            require_once("../php/std-date.php");

            $url = $_SERVER['REQUEST_URI'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $errorTime = stdDate();
            $IPAddress = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : "not-available";
            $browser = getBrowser();
            $OS = getOS();

            $plaintext_body = "URL: ".$url;
            $plaintext_body .= "\r\nUser Agent: ".$userAgent;
            $plaintext_body .= "\r\nTime: ".$errorTime;
            $plaintext_body .= "\r\nIP Address: ".$IPAddress;
            $plaintext_body .= "\r\nBrowser: ".$browser;
            $plaintext_body .= "\r\nOS: ".$OS;

            $html_body = "";

            sendEmailSES("errors@denvelope.com", "500 Internal Server Error", $plaintext_body, $html_body);
        }
    ?>

    <div id="link-container">
        <a href=""><i class="fas fa-home"></i> Home</a>
        <?php
            if(!$betaHide){
                echo '<a href="signup"><i class="fas fa-user-plus"></i> '; echo getTranslatedContent("errors_button_signup"); echo'</a>
                    <a href="login"><i class="fas fa-sign-in-alt"></i> '; echo getTranslatedContent("errors_button_login"); echo'</a>
                ';
            }
        ?>
    </div>
</body>
</html>