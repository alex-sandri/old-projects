<?php
    if(isset($_GET['u']) && isset($_GET['t']) && !empty($_GET['u']) && !empty($_GET['t'])){
        require("../php/dbh.php");
        require("../php/send-email-account-confirmed.php");

        $sqlQuery = "UPDATE users SET activated=1 WHERE userID=? AND createdUnixTime=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_GET['u'], $_GET['t']);
        mysqli_stmt_execute($stmt);

        $rowsAffected = mysqli_stmt_affected_rows($stmt);

        $sqlQuery = "SELECT * FROM users WHERE userID=? AND createdUnixTime=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_GET['u'], $_GET['t']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if($rowsAffected == 0){
            header("Location: ../");
            exit();
        }

        sendEmailAccountConfirmed($user['email']);
    }
    else{
        header("Location: ../");
        exit();
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
    require_once("../lang/".$lang.".php");
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
    <meta http-equiv="refresh" content="10;URL=../login">
    <title><?php echo getTranslatedContent("confirm_account_title"); ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/pace.js"></script>
    <script src="../js/ace/src-min-noconflict/ace.js"></script>
    <script src="../js/ace/src-min-noconflict/ext-language_tools.js"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
</head>
<body>
    <div class="progress-bar-redirect"></div>
    <div id="congratulations"><?php echo 'using System;
using System.Threading;
using Denvelope;

using static System.Console;
using static System.Threading.Thread;

namespace '; echo getTranslatedContent("confirm_account_namespace_congratulations"); echo'
{
    class Program
    {
        static void Main(string[] args)
        {
            string '; echo getTranslatedContent("confirm_account_username"); echo' = "'; echo $user['username']; echo '";

            WriteLine($"'; echo getTranslatedContent("confirm_account_congratulations_username"); echo'");

            WriteLine("'; echo getTranslatedContent("confirm_account_redirect_to_login"); echo'");

            Sleep(10000);

            Redirect.ToLogin();
        }
    }
}';
        ?>
    </div>

    <script>
        var editor = ace.edit("congratulations");
        editor.setTheme("ace/theme/dracula");
        $(".ace_editor").css("height", "100vh");
        $(".ace_content").css("background-color", "var(--body-color)");
        $(".ace_gutter").css("background-color", "var(--body-color)");
        $(".ace_gutter").css("color", "var(--text-color)");
        editor.session.setMode("ace/mode/csharp");
        editor.setOptions({
            printMarginColumn: -1,
            highlightActiveLine: false,
            highlightSelectedWord: false,
            hScrollBarAlwaysVisible: false,
            vScrollBarAlwaysVisible: false,
            highlightGutterLine: false,
            fixedWidthGutter: true,
            enableBasicAutocompletion: true,
            enableLiveAutocompletion: true,
            displayIndentGuides: false,
            readOnly: true,
            fontSize: "15px",
            wrap: false,
        });

        var content = editor.getSession().getValue();
        editor.getSession().setValue("");

        var i = 0;

        writeCode(content);

        function writeCode(content){
            if(i < content.length){
                typedContent = editor.getSession().getValue();
                editor.getSession().setValue(typedContent + content[i]);
                i++;
            }

            setTimeout(() => {
                writeCode(content);
            }, 5);
        }
    </script>

    <style>
        .progress-bar-redirect{
            position: absolute;
            height: 1%;
            background-color: var(--text-color);
            z-index: 999;
            bottom: 0;
            animation: progress 10s linear;
        }

        @keyframes progress{
            from{
                width: 0%;
            }
            to{
                width: 100%;
            }
        }
    </style>
</body>
</html>