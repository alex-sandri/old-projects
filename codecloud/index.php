<?php
    session_start();

    if(isset($_COOKIE['userSession'])){
        $sessionID = $_COOKIE['userSession'];

        if(!ctype_xdigit($sessionID)){
            $cookieError = "notValidCookie";

            header("Location: ");
            exit();
        }

        require("php/dbh.php");

        $sqlQuery = "SELECT * FROM sessions WHERE sessionID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $sqlError = "sqlError";

            header("Location: ");
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
                            
                $sqlQuery = "UPDATE sessions SET sessionID=? WHERE username=? AND email=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $sqlError = "sqlError";

                    header("Location: ");
                    exit();
                }
                else{
                    mysqli_stmt_bind_param($stmt, "sss", $sessionID, $_SESSION['username'], $_SESSION['email']);
                    mysqli_stmt_execute($stmt);
                            
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];

                    setcookie("userSession", $sessionID, time() + 86400 * 30, "/", "");

                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);

                    header("Location: account");
                }
            }
            else{
                header("Location: php/logout.php");
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#160C28">
    <meta name="msapplication-navbutton-color" content="#160C28">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>code.cloud - Save here any code in any language</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/signup-login-form.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,900" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
    <script src="js/signup-login-toggle.js"></script>
    <script src="js/forgot-password.js"></script>
    <script src="js/pace.js"></script>
</head>
<body id="body">

    <?php
        require("php/header.php");
    ?>

    <?php
        if(!isset($_SESSION['username'])){
            require("php/signup-login-form.php");
        }
    ?>

    <div class="main-div-home" id="main-div-home">
        <h1 class="top-h1-home">A Simple, Fast and Reliable way</h1>
        <h3 class="top-h3-home">to store code in any language you want</h3>
    </div>

    <?php
        require("php/footer.php");
    ?>

    <?php
        if(!isset($_SESSION['loginError']) && !isset($_SESSION['signupSuccess'])){
            //open the signup form only if a login error has not occured and if the user has not just signed up
            echo '<script>if($(window).width() > 1200){signUp();}</script>';
        }
    ?>

    <script>
        var resizedUnder = false;
        var previousWidth = $(window).width();
        var orientation = window.orientation;

        $("#main-div-home").css("margin-top", $("#header").outerHeight(true) + ($(document).outerHeight(true) / 2.5) - ($("#main-div-home").outerHeight() / 2));

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

            $("#main-div-home").css("margin-top", $("#header").outerHeight(true) + ($(document).outerHeight(true) / 2.5) - ($("#main-div-home").outerHeight() / 2));
        });

        $(window).on("load", function(){
            $("#main-div-home").css("margin-top", $("#header").outerHeight(true) + ($(document).outerHeight(true) / 2.5) - ($("#main-div-home").outerHeight() / 2));
        });
    </script>

</body>
</html>
