<?php
    session_start();

    $enableIPLocation = true;

    if($enableIPLocation){
        if(!isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
            require("../php/translate-from-location.php");
        }
    }

    require("../php/dbh.php");
    require("../php/get-current-user.php");
    require("../php/global-vars.php");
    require_once("../php/create-cookie.php");

    if(isset($_COOKIE['userSession'])){
        $sessionID = $_COOKIE['userSession'];

        if(!ctype_xdigit($sessionID)){
            $cookieError = "notValidCookie";

            require("../php/delete-cookie.php");
            deleteCookie("userSession");

            header("Location: ../");
            exit();
        }

        $sqlQuery = "SELECT * FROM sessions WHERE sessionID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $sqlError = "sqlError";

            header("Location: ../");
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

                    header("Location: ../");
                    exit();
                }
                else{
                    mysqli_stmt_bind_param($stmt, "ssss", $sessionID, $_SESSION['username'], $_SESSION['email'], $_COOKIE['userSession']);
                    mysqli_stmt_execute($stmt);
                                
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];

                    createCookie("userSession", $sessionID);

                    mysqli_stmt_close($stmt);
                }
            }
            else{
                header("Location: ../php/logout.php?ref=contact");
            }
        }
    }

    if(!isset($_SESSION['username'])){
        header("Location: ../login/?ref=contact");
        exit();
    }

    $user = getUser();

    if(isset($_COOKIE['userSession'])){
        require("../php/update-last-activity.php");

        updateLastActivity($sessionID);
    }
?>

<?php
    require("../php/global-vars.php");
?>

<?php
    $betaHide = false;

    if($betaHide){
        header("Location: ../");
        exit();
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
    <title><?php echo getTranslatedContent("contact_us_title"); ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <link rel="stylesheet" href="../css/signup-login-pages.css">
    <link rel="stylesheet" href="../css/account.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/complete-forms-validation.php"></script>
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

    <div class="contact-form" id="contact-form">
        <h2 class="contact-form-h2"><?php echo getTranslatedContent("contact_us_title"); ?></h2>
        <br>
        <form action="../php/contact.php" method="post">
            <div class="input-div">
                <?php
                    if(isset($_SESSION['contactSubjectError'])){
                        if($_SESSION['contactSubjectError'] == "emptySubject"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("contact_error_subject_empty"); echo'</p>';
                        }
                        else if($_SESSION['contactSubjectError'] == "subjectTooLong"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("contact_error_subject_too_long"); echo'</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="subject-error"></p>
                <div id="subject-field-container" style="display: flex; border-radius: 5px;">
                    <input type="text" class="input" name="subject" id="subject" placeholder="<?php echo getTranslatedContent("contact_us_subject"); ?>" maxlength="100">
                    <div class="input-icon">
                        <i class="fas fa-heading"></i>
                    </div>
                </div>
                <div class="char-counter-contact-form-subject-div">
                    <span>0</span> / 100
                </div>
            </div>
            <br>
            <div class="input-div">
                <?php
                    if(isset($_SESSION['contactMessageError'])){
                        if($_SESSION['contactMessageError'] == "emptyMessage"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("contact_error_message_empty"); echo'</p>';
                        }
                        else if($_SESSION['contactMessageError'] == "messageTooLong"){
                            echo '<p class="error-input-field">'; echo getTranslatedContent("contact_error_message_too_long"); echo'</p>';
                        }
                    }
                ?>
                <p class="error-input-field" id="message-error"></p>
                <div id="message-field-container" style="border-radius: 5px;">
                    <div style="display: flex; border-radius: 5px;">
                        <textarea name="message" id="message" rows="10" maxlength="5000" class="contact-textarea" placeholder="<?php echo getTranslatedContent("contact_us_message"); ?>"></textarea>
                        <div class="input-icon">
                            <i class="fas fa-comment-alt"></i>
                        </div>
                    </div>
                    <div class="char-counter-contact-form-message-div">
                        <span>0</span> / 5000
                    </div>
                </div>
            </div>
            <br>
            <button type="submit" class="submit-button" name="contact-button" id="contact-button"><?php echo getTranslatedContent("contact_us_submit"); ?></button>
        </form>
    </div>

    <?php
        unset($_SESSION['contactError']);
        unset($_SESSION['contactSubjectError']);
        unset($_SESSION['contactMessageError']);
    ?>

    <?php
        if(isset($_COOKIE['consent'])){
            echo '<script>
                    $("#contact-form").css("margin-top", $("#header").outerHeight(true));

                    $(window).on("load", function(){
                        $("#contact-form").css("margin-top", $("#header").outerHeight(true));
                    });
            
                    $(window).resize(function(){
                        if($("#menu-mob").css("display") == "none"){
                            $("#contact-form").css("margin-top", $("#header").outerHeight(true));
                        }
                    });
                </script>
            ';
        }
    ?>
</body>
</html>