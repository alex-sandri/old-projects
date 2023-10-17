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
                header("Location: ../php/logout.php?ref=supportcenter");
                exit();
            }
        }
    }

    if(!isset($_SESSION['username'])){
        header("Location: ../login/?ref=supportcenter");
        exit();
    }

    $user = getUser();

    if($user['accountType'] != "admin" && $user['accountType'] != "support"){
        header("Location: ../");
        exit();
    }

    if(isset($_GET['case'])){
        require("../php/get-support-case.php");

        $case = getSupportCase($_GET['case']);

        if(!$case){
            header("Location: ./");
            exit();
        }
    }
?>

<?php
    if(isset($_COOKIE['userSession'])){
        require("../php/update-last-activity.php");

        updateLastActivity($sessionID);
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
    <title><?php echo getTranslatedContent("support_center_title"); ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <link rel="stylesheet" href="../css/signup-login-pages.css">
    <link rel="stylesheet" href="../css/support-center.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/pace.js"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
</head>
<body>
    <?php
        $betaDisableAccessHeader = false;
        
        require("../php/header.php");
    ?>

    <div class="support-main-div" id="support-main-div">
        <h2 class="signup-form-h2" style="margin: 0; display: flex; flex-direction: column; word-break: break-word;"><?php echo isset($_GET['case']) ? $case['title']."<br><span style='font-size: 25px;'>#".$case['caseNumber']."</span>" : getTranslatedContent("support_center_title") ?></h2>   
        <br> 
        <?php
            if(!isset($_GET['case'])){
                echo '<div class="support-cases-container">';
                    require("../php/get-support-cases.php");
            
                    $supportCases = getSupportCases();
            
                    $i = 0;
            
                    foreach ($supportCases as $case) {
                        echo '<div class="support-case" id="sc-'; echo $i; echo'">
                                <div class="support-case-icon">
                                    <i class="fas fa-comment-alt"></i>
                                </div>
                                <div class="support-case-info">
                                    <h3>'; echo $case['title']; echo'</h3>
                                    <h4>#'; echo $case['caseNumber']; echo'</h4>
                                </div>
                            </div>
                        ';
    
                        $i++;
                    }
            
                    if($i == 0){
                        echo '<div class="empty-folder-div" style="width: 96vw; margin-top: 5vw;">
                                <h3 style="margin-bottom: 0;">'; echo getTranslatedContent("support_center_no_open_support_cases"); echo'</h3>
                                <img src="../img/humaaans/sitting-2.svg" alt="" style="max-width: 100%;">
                            </div>
                        ';
                    }

                    echo'</div>
                ';
            }
            else{
                echo '<div class="support-messages-container">';
                    require("../php/get-support-messages.php");

                    $supportMessages = getSupportMessages($case['caseNumber']);

                    $i = 0;
                
                    foreach ($supportMessages as $message) {
                        $time = substr($message['time'], 0, strripos($message['time'], " "));

                        if($message['username'] != "support"){
                            echo '<div class="user">
                                    <div class="support-message" id="sm-'; echo $i; echo'">
                                        <p>'; echo $message['body']; echo'</p>
                                        <p class="sender-info">'; echo $message['username']." ".$time; echo'</p>
                                    </div>
                                </div>
                            ';
                        }
                        else{
                            echo '<div class="support">
                                    <div class="support-message" id="sm-'; echo $i; echo'">
                                        <p>'; echo $message['body']; echo'</p>
                                        <p class="sender-info">'; echo $message['username']." ".$time; echo'</p>
                                    </div>
                                </div>
                            ';
                        }
                
                        $i++;
                    }
                
                echo'</div>';
            }
        ?>
        
        <?php
            if(isset($_GET['case']) && $case['status'] != "closed"){
                echo '<form action="../php/support-reply.php" method="post">
                        <input type="hidden" name="case-number" value="'; echo $case['caseNumber']; echo'">
                        <div class="message-bottom-bar">
                            ';
                            if(isset($_SESSION['replyMessageError']) && $_SESSION['replyMessageError'] !== false){
                                if($_SESSION['replyMessageError'] == "emptyReplyMessage"){
                                    echo '<p class="error-message" id="error-message-reply">'; echo getTranslatedContent("support_center_error_reply_message_empty"); echo'</p>';
                                }
                                else if($_SESSION['replyMessageError'] == "replyMessageTooLong"){
                                    echo '<p class="error-message" id="error-message-reply">'; echo getTranslatedContent("support_center_error_reply_message_too_long"); echo'</p>';
                                }
                            }
                            echo'
                            <div class="message-bottom-bar-input-button-container">
                                    <div class="message-bottom-bar-input">
                                        <input type="text" name="message" id="message" class="input" placeholder="'; echo getTranslatedContent("support_center_message"); echo'" maxlength="5000">
                                    </div>
                                    <div class="message-bottom-bar-send-button">
                                        <button type="submit" name="send-reply-button" id="send-reply-button"><i class="fas fa-paper-plane"></i></button>
                                    </div>
                            </div>
                            <div class="counter-label-container">
                                <p class="char-counter-support-message"><span>0</span> / 5000</p>
                                <label for="mark-as-closed" class="remember-me-container" style="margin: 0; font-size: 16px; padding: 2px 0 0 35px;">
                                    <input type="checkbox" name="mark-as-closed" id="mark-as-closed" value="false">
                                    <span class="checkmark"></span>   
                                    '; echo getTranslatedContent("support_center_mark_as_closed"); echo'
                                </label>
                            </div>
                        </div>
                    </form>
                ';
            }
        ?>
    </div>

    <?php
        unset($_SESSION['replyMessageError']);
    ?>

    <script>
        $(document).ready(function(){
            $(".support-case").click(function(){
                var caseNumber = $(this).children(".support-case-info").children("h4").html().substr(1);

                window.location.href = "./?case=" + caseNumber;
            });

            $("#message").on("keydown input", function(){
                $(".char-counter-support-message span").html($("#message").val().length);
            });

            $("#mark-as-closed").click(function(){
                if($("#mark-as-closed").is(":checked")){
                    $("#mark-as-closed").val("true");
                }
                else{
                    $("#mark-as-closed").val("false");
                }
            });

            $("#send-reply-button").click(function(e){
                if($("#message").val().length > 5000){
                    $("#error-message-reply").html("<?php echo getTranslatedContent("support_center_error_reply_message_too_long"); ?>");
                    e.preventDefault();
                }
                else if($("#message").val().length == 0){
                    $("#error-message-reply").html("<?php echo getTranslatedContent("support_center_error_reply_message_empty"); ?>");
                    e.preventDefault();
                }
            });
        });

        $("#support-main-div").css("margin-top", $("#header").outerHeight(true));

        $(window).on("load", function(){
            $("#support-main-div").css("margin-top", $("#header").outerHeight(true));
        });

        $(window).resize(function(){
            if($("#menu-mob").css("display") == "none"){
                $("#support-main-div").css("margin-top", $("#header").outerHeight(true));
            }
        });
    </script>
</body>
</html>