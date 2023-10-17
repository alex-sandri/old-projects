<?php
    if(!isset($_SESSION)){
        session_start();
    }

    require_once("php/global-vars.php");

    if(isset($_SESSION['username'])){
        header("Location: $urlPrefix"."account");
        exit();
    }

    if(isset($_COOKIE['userSession'])){
        $sessionID = $_COOKIE['userSession'];

        if(!ctype_xdigit($sessionID)){
            $cookieError = "notValidCookie";

            require("php/delete-cookie.php");
            deleteCookie("userSession");

            header("Location: ./");
            exit();
        }

        require("php/dbh.php");
        require_once("php/create-cookie.php");

        $sqlQuery = "SELECT * FROM sessions WHERE sessionID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $sqlError = "sqlError";

            header("Location: ./");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $sessionID);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if($row){
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];

                $sessionID = bin2hex(random_bytes(64));
                            
                $sqlQuery = "UPDATE sessions SET sessionID=? WHERE (username=? OR email=?) AND sessionID=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $sqlError = "sqlError";

                    header("Location: ./");
                    exit();
                }
                else{
                    mysqli_stmt_bind_param($stmt, "ssss", $sessionID, $_SESSION['username'], $_SESSION['email'], $_COOKIE['userSession']);
                    mysqli_stmt_execute($stmt);
                                
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];

                    createCookie("userSession", $sessionID);

                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);

                    header("Location: $urlPrefix"."account");
                    exit();
                }
            }
            else{
                header("Location: $urlPrefix"."php/logout.php?ref=home");
                exit();
            }
        }
    }
?>

<?php
    $enableIPLocation = true;

    if($enableIPLocation){
        if(!isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
            require("php/translate-from-location.php");
        }
    }
?>

<?php
    require("php/global-vars.php");
?>

<?php
    /*
    if(isset($_GET['betakey']) && $_GET['betakey'] == "ilovedoingbetatesting"){
        $betaDisableAccess = false;
        $betaDisableAccessHeader = false;
        $betaDisableAccessHeaderLoggedOut = false;
        $betaHide = false;

        unset($_SESSION['betaTestKeyOnReaddress']);
        unset($_SESSION['betaKey']);

        $_SESSION['betaTestKeyOnReaddress'] = true;
        $_SESSION['betaKey'] = "ilovedoingbetatesting";
    }
    else{
        $betaDisableAccess = true;
        $betaDisableAccessHeader = true;
        $betaDisableAccessHeaderLoggedOut = true;
        $betaHide = true;
    }
    */

    $betaDisableAccess = false;
    $betaDisableAccessHeader = false;
    $betaDisableAccessHeaderLoggedOut = false;
    $betaHide = false;
?>

<?php
    require("lang/".$lang.".php");
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
    <meta name="description" content="<?php echo getTranslatedContent("home_description"); ?>" />
    <meta name="msvalidate.01" content="1BAD577074DD2784376721D77CC3D921" />
    <title>Denvelope<?php //echo " - ".getTranslatedContent("home_title"); ?></title>
    <link rel="alternate" hreflang="en" href="https://denvelope.com/" />
    <link rel="alternate" hreflang="it" href="https://denvelope.com/it/" />
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <!--
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/signup-login-form.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/account.css">
    -->
    <link rel="stylesheet" href="css/main.min.css">
    <script rel="dns-prefetch" src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <?php
        if(!$betaDisableAccess){
            echo '<script async src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
                <script src="js/signup-login-toggle.js"></script>
                <script src="js/forgot-password.js"></script>
            ';
        }
    ?>
    <script src="js/pace.js"></script>
    <?php
        if($isProduction && !$betaDisableAccess){
            echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <script>
                    function onSubmitSignUp(token) {
                        document.getElementById("form-signup-form").submit();
                    }

                    function onSubmitLogIn(token) {
                        document.getElementById("form-login-form").submit();
                    }
                </script>
            ';
        }
    ?>
</head>
<body id="body" style="overflow-x: unset;">

    <?php
        if(!isset($_COOKIE['consent']) && !$betaDisableAccess){
            require("php/cookie-banner.php");
        }
    ?>

    <?php
        $noSignupLoginFormHeader = false;

        require("php/header.php");
    ?>

    <?php
        if(!isset($_SESSION['username']) && !$betaDisableAccess){
            require("php/signup-login-form.php");
        }
    ?>

    <div class="main-div-home" id="main-div-home">
        <div id="text-div-home">
            <h1 class="top-h1-home"><?php echo getTranslatedContent("home_hero_title"); ?></h1>
            <h3 class="top-h3-home"><?php echo getTranslatedContent("home_hero_subtitle"); ?></h3>

            <?php
                if($betaDisableAccess){
                    echo '<h3 class="top-h3-home" style="font-size: 20px; margin-top: 5vw; text-transform: uppercase; color: #79B473; margin-bottom: 2vw;">'; echo "//".getTranslatedContent("home_beta_message"); echo'</h3>';
                }
                else{
                    echo '<button class="signup-button-home-mob" id="signup-button-home-mob">'; echo getTranslatedContent("home_signup_button_mob"); echo'</button>';
                }
            ?>
        </div>

        <?php
            if(!$betaDisableAccess){
                ?>
                    <div>
                        <div class="home-feature-container">
                            <img src="img/store.svg" alt="" loading="lazy">
                            <div>
                                <h2><?php echo getTranslatedContent("home_feature_store_title"); ?></h2>
                                <p><?php echo getTranslatedContent("home_feature_store_description"); ?></p>
                            </div>
                        </div>
                        <div class="home-feature-container">
                            <img src="img/view-edit-code.svg" alt="" loading="lazy">
                            <div>
                                <h2><?php echo getTranslatedContent("home_feature_view_edit_title"); ?></h2>
                                <p><?php echo getTranslatedContent("home_feature_view_edit_description"); ?></p>
                            </div>
                        </div>
                        <div class="home-feature-container">
                            <img src="img/share.svg" alt="" loading="lazy">
                            <div>
                                <h2><?php echo getTranslatedContent("home_feature_share_title"); ?></h2>
                                <p><?php echo getTranslatedContent("home_feature_share_description"); ?></p>
                            </div>
                        </div>
                    </div>
                <?php
            }
            else{
                echo '<style>.main-div-home{height: 100vh;}</style>';
            }
        ?>

        <?php
            require("php/footer.php");
        ?>
    </div>

    <?php
        if(!isset($_SESSION['loginError']) && !isset($_SESSION['signupSuccess']) && !$betaDisableAccess){
            //open the signup form only if a login error has not occured and if the user has not just signed up
            echo '<script>if($(window).width() > 1200){signUp();}</script>';
        }
    ?>

    <script>
        var resizedUnder = false;
        var previousWidth = $(window).width();
        var orientation = window.orientation;

        $("#signup-button-home-mob").click(function(){
            openMenuMob();
            signUp();
        });

        $("#text-div-home").css("margin-top", $(".header-main-div").outerHeight(true) + ($("#header").css("padding") * 2));
        
        $(window).resize(function(){
            if($(window).width() <= 1200 && $(window).width() != previousWidth && window.orientation != orientation){
                closeForm();
                $('.signup-login-header').css('display', 'none');
                resizedUnder = true;
                previousWidth = $(window).width();
            }
            else if($(window).width() > 1200 && resizedUnder && $(window).width() != previousWidth && window.orientation != orientation){
                signUp();
                $('.signup-login-header').css('display', 'block');
                resizedUnder = false;
                previousWidth = $(window).width();
            }

            if($(window).width() > 1200 && ($(".signup-login-form").css("display") == "none" || ($("#signup-form").css("display") == "none" && $("#login-form").css("display") == "none" && $("#forgot-password-form").css("display") == "none"))){
                signUp();
            }

            $("#text-div-home").css("margin-top", $(".header-main-div").outerHeight(true) + ($("#header").css("padding") * 2));
        });

        $(window).on("load", function(){
            $("#text-div-home").css("margin-top", $(".header-main-div").outerHeight(true) + ($("#header").css("padding") * 2));
        });

        $(document).on("dragstart", function(){
            return false;
        });
    </script>

    <?php
        if($betaDisableAccess){
            echo '<style>.main-div-home, #text-div-home{width: 100%}</style>';
        }
    ?>

</body>
</html>
