<?php
    if(isset($_GET['sid'])){
        session_start();

        require("../php/dbh.php");
        require("../php/get-file.php");

        $file = getFile($_GET['sid']);

        if($file == null){
            header("Location: ../");
            exit();
        }
    }
    else if(isset($_GET['fid'])){
        require("../php/dbh.php");
        require("../php/get-folder.php");

        $folder = getFolder($_GET['fid']);

        if($folder == null){
            header("Location: ../");
            exit();
        }
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
    <title>
    <?php 
        if(isset($_GET['sid'])){
            echo $file['name']." - Denvelope";
        }
        else{
            if(isset($_GET['t']) && $_GET['t'] == "s"){
                $file = getFile($_GET['iid']);

                echo $file['name']." - Denvelope";
            }
            else {
                echo $folder['name']." - Denvelope";
            }
        }
    ?>
    </title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/devicon/2.2/devicon.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/pace.js"></script>
    <script src="../js/add-source-code.php"></script>
    <script src="../js/ace/src-min-noconflict/ace.js"></script>
    <script src="../js/ace/src-min-noconflict/ext-language_tools.js"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
</head>
<body id="body" <?php if (isset($_GET['sid']) || (isset($_GET['iid']) && (isset($_GET['t']) && $_GET['t'] == "s"))) echo 'style="height: calc(100vh - 2vw - 4px); width: calc(100vw - 2vw - 4px); padding: 1vw; border: 2px solid var(--text-color);"'; ?>>
    
    <?php
        if(!isset($_COOKIE['consent'])){
            require("../php/cookie-banner.php");
        }
    ?>

    <div class="file-save-success-msg" id="success-msg">
        <p></p>
    </div>
    <div class="file-save-success-msg" id="error-msg">
        <p></p>
    </div>
    
    <?php
        if(isset($_GET['sid'])){
            require_once("../php/show-file.php");

            showFile($file, true, false); 
        }
        else if(isset($_GET['fid'])){
            require_once("../php/show-folder.php");

            if(isset($_GET['iid']) && isset($_GET['t'])){ //internal ID == folderID / fileID, it cannot be of a folder / file out of this folder. t = type (file / folder)
                if($_GET['t'] == "s"){
                    require_once("../php/show-file.php");
                    require_once("../php/get-file.php");

                    $file = getFile($_GET['iid']);

                    if(strpos($file['pathToThis'], $folder['pathToThis']) === false){ //block access to files above the shared folder, and files not within this folder
                        header("Location: ../");
                        exit();
                    }

                    if($file == null){
                        header("Location: ../");
                        exit();
                    }

                    showFile($file, true, true);
                }
                else if($_GET['t'] == "f"){
                    require_once("../php/get-folder.php");

                    $folderIID = getFolder($_GET['iid']);

                    if(strpos($folderIID['pathToThis'], $folder['pathToThis']) === false){ //block access to folders above the shared one, and folders not within this folder
                        header("Location: ../");
                        exit();
                    }

                    if($folderIID == null){
                        header("Location: ../");
                        exit();
                    }

                    showFolder($_GET['iid']);
                }
            }
            else{
                showFolder($folder['folderID']);
            }
        }
    ?>
</body>
</html>