<?php
    session_start();

    $sessionID = "";

    require(dirname(__FILE__, 3)."/php/dbh.php");
    require(dirname(__FILE__, 3)."/php/global-vars.php");
    require_once(dirname(__FILE__, 3)."/php/create-cookie.php");
    require(dirname(__FILE__, 3)."/php/get-email-preferences.php");

    if(isset($_COOKIE['userSession'])){
        
        $sessionID = $_COOKIE['userSession'];

        if(!ctype_xdigit($sessionID)){
            $cookieError = "notValidCookie";

            require(dirname(__FILE__, 3)."../../php/delete-cookie.php");
            deleteCookie("userSession");

            header("Location: ../../");
            exit();
        }

        $sqlQuery = "SELECT * FROM sessions WHERE sessionID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $sqlError = "sqlError";

            header("Location: ../../");
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

                    header("Location: ../../");
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
                header("Location: ../../php/logout.php?ref=settings");
                exit();
            }
        }
    }

    if(!isset($_SESSION['username'])){
        header("Location: ../../login/?ref=settings");
        exit();
    }

    $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
        $sqlError = "sqlError";

        header("Location: ../../");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    $_SESSION['plan'] = $user['plan'];

    $emailPreferences = getEmailPreferences();
?>

<?php
    $enablePayments = false;
    $enableThemes = false;
    $betaDisableAccess = false;
    $betaDisableAccessHeader = false;
?>

<?php
    if(isset($_COOKIE['userSession'])){
        require(dirname(__FILE__, 3)."/php/update-last-activity.php");

        updateLastActivity($sessionID);
    }
?>

<?php
    require(dirname(__FILE__, 3)."/lang/".$lang.".php");
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
    <title><?php echo getTranslatedContent("settings_title"); ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <!--
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/signup-login-form.css">
    <link rel="stylesheet" href="../../css/account.css">
    <link rel="stylesheet" href="../../css/footer.css">
    <link rel="stylesheet" href="../../css/icons.css">
    <link rel="stylesheet" href="../../css/support-center.css">
    -->
    <link rel="stylesheet" href="../../css/payment-font/css/paymentfont.min.css">
    <link rel="stylesheet" href="../../css/main.min.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/authy-form-helpers/2.3/form.authy.min.css" media="screen" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../../js/account-settings-toggle.js"></script>
    <script src="../../js/signup-login-toggle.js"></script>
    <script src="../../js/pace.js"></script>
    <?php
        if($enablePayments){
            echo '<script src="https://js.stripe.com/v3/"></script>';
        }
    ?>
    <script src="//cdnjs.cloudflare.com/ajax/libs/authy-form-helpers/2.3/form.authy.min.js" type="text/javascript"></script>
</head>
<body id="body">

    <div class="file-save-success-msg" id="success-msg">
        <p><i class='fas fa-check'></i> <?php echo getTranslatedContent("settings_updated_correctly"); ?></p>
    </div>
    <div class="file-save-success-msg" id="error-msg">
        <p><i class='fas fa-times'></i> <?php echo getTranslatedContent("settings_update_error"); ?></p>
    </div>

    <?php
        require(dirname(__FILE__, 3)."/php/header.php");
    ?>

    <div class="main-div-account-settings" id="main-div-account-settings">
        <div class="account-settings-div" id="account-settings-div">
            <h2 class="account-settings-h2"><?php echo getTranslatedContent("settings_side_menu_title"); ?></h2>
            <a onclick="general()" href="#general" class="account-settings-a account-settings-a-selected" id="account-settings-a-gen"><?php echo getTranslatedContent("settings_general"); ?> <i class="fas fa-cog"></i></a>
            <?php
                if($enablePayments){
                    echo '<br>
                        <a onclick="plan()" href="#plan" class="account-settings-a" id="account-settings-a-pla">'; echo getTranslatedContent("settings_plan"); echo' <i class="fas fa-credit-card"></i></a>
                    ';
                }
            ?>
            <br>
            <a onclick="security()" href="#security" class="account-settings-a" id="account-settings-a-sec"><?php echo getTranslatedContent("settings_security"); ?> <i class="fas fa-shield-alt"></i></a>
            <br>
            <a onclick="advanced()" href="#advanced" class="account-settings-a" id="account-settings-a-adv"><?php echo getTranslatedContent("settings_advanced"); ?> <i class="fas fa-sliders-h"></i></a>
            <br>
            <a onclick="info()" href="#info" class="account-settings-a" id="account-settings-a-inf"><?php echo getTranslatedContent("settings_info"); ?> <i class="fas fa-info-circle"></i></a>
            <br>
            <a onclick="privacy()" href="#privacy" class="account-settings-a" id="account-settings-a-pri"><?php echo getTranslatedContent("settings_privacy"); ?> <i class="fas fa-user-shield"></i></a>
            <hr class="account-settings-hr settings-separator-hr">
            <a onclick="support()" href="#support" class="account-settings-a" id="account-settings-a-sup"><?php echo getTranslatedContent("settings_support"); ?> <i class="fas fa-envelope"></i></a>
        </div>
    </div>
    <div class="main-div-account-settings-mob" id="main-div-account-settings-mob" style="z-index: 999;">
        <a class="account-settings-arrow-down-mob" id="account-settings-arrow-down-mob"><i class="fas fa-chevron-down"></i></a>
        <div class="account-settings-div-mob" id="account-settings-div-mob">
            <a onclick="general()" href="#general" class="account-settings-a account-settings-a-selected" id="account-settings-a-gen-mob"><?php echo getTranslatedContent("settings_general"); ?> <i class="fas fa-cog"></i></a>
            <?php
                if($enablePayments){
                    echo '<br>
                        <a onclick="plan()" href="#plan" class="account-settings-a" id="account-settings-a-pla-mob">'; echo getTranslatedContent("settings_plan"); echo' <i class="fas fa-credit-card"></i></a>
                    ';
                }
            ?>
            <br>
            <a onclick="security()" href="#security" class="account-settings-a" id="account-settings-a-sec-mob"><?php echo getTranslatedContent("settings_security"); ?> <i class="fas fa-shield-alt"></i></a>
            <br>
            <a onclick="advanced()" href="#advanced" class="account-settings-a" id="account-settings-a-adv-mob"><?php echo getTranslatedContent("settings_advanced"); ?> <i class="fas fa-sliders-h"></i></a>
            <br>
            <a onclick="info()" href="#info" class="account-settings-a" id="account-settings-a-inf-mob"><?php echo getTranslatedContent("settings_info"); ?> <i class="fas fa-info-circle"></i></a>
            <br>
            <a onclick="privacy()" href="#privacy" class="account-settings-a" id="account-settings-a-pri-mob"><?php echo getTranslatedContent("settings_privacy"); ?> <i class="fas fa-user-shield"></i></a>
            <hr class="account-settings-hr settings-separator-hr">
            <a onclick="support()" href="#support" class="account-settings-a" id="account-settings-a-sup-mob"><?php echo getTranslatedContent("settings_support"); ?> <i class="fas fa-envelope"></i></a>
        </div>
    </div>
    <div class="account-settings" id="account-settings">
        <div class="general-settings" id="general-settings">
            <h4 class="account-settings-h4"><?php echo getTranslatedContent("settings_general_username"); ?></h4>
            <div style="display: flex; border-radius: 5px; width: 30%; flex-direction: column;" class="edit-container">
                <form action="../../php/change-user-info.php" method="post">
                    <div style="display: flex;">
                        <input type="text" class="input input-disabled" name="username-account-settings" id="username-account-settings" placeholder="<?php echo getTranslatedContent("settings_general_username"); ?>" style="width: 90%; border-radius: 5px; border-top-right-radius: 0px; border-bottom-right-radius: 0px;" value="<?php echo $_SESSION['username'] ?>">
                        <div class="input-icon" id="username-edit-button" style="cursor: pointer;">
                            <i class="fas fa-edit"></i>
                        </div>
                    </div>
                    <button type="submit" class="account-settings-change-button" id="account-settings-change-button-username" name="account-settings-change-button-username"><i class="fas fa-check"></i> <?php echo getTranslatedContent("settings_general_confirm"); ?></button>
                </form>
            </div>
            <hr class="account-settings-hr">
            <h4 class="account-settings-h4"><?php echo getTranslatedContent("settings_general_email"); ?></h4>
            <div style="display: flex; border-radius: 5px; width: 30%; flex-direction: column;" class="edit-container">
                <form action="../../php/change-user-info.php" method="post">
                    <div style="display: flex;">
                        <input type="text" class="input input-disabled" name="email-account-settings" id="email-account-settings" placeholder="<?php echo getTranslatedContent("settings_general_email"); ?>" style="width: 90%; border-radius: 5px; border-top-right-radius: 0px; border-bottom-right-radius: 0px;" value="<?php echo $_SESSION['email'] ?>">
                        <div class="input-icon" id="email-edit-button"  style="cursor: pointer;">
                            <i class="fas fa-edit"></i>
                        </div>
                    </div>
                    <button class="account-settings-change-button" id="account-settings-change-button-email" name="account-settings-change-button-email"><i class="fas fa-check"></i> <?php echo getTranslatedContent("settings_general_confirm"); ?></button>
                </form>
            </div>
            <hr class="account-settings-hr">
            <h4 class="account-settings-h4"><?php echo getTranslatedContent("settings_general_language"); ?></h4>
            <select name="language" id="language" class="language-toggle">
                <option value="en" <?php if((isset($_COOKIE['lang']) && $_COOKIE['lang'] == "en") || !isset($_COOKIE['lang']) || $lang == "en") echo "selected"; ?>>English</option>
                <option value="it" <?php if((isset($_COOKIE['lang']) && $_COOKIE['lang'] == "it") || $lang == "it") echo "selected"; ?>>Italiano</option>  
            </select>
            <?php
                if($enableThemes){
                    echo '<hr class="account-settings-hr">
                        <h4 class="account-settings-h4">'; echo getTranslatedContent("settings_general_theme"); echo'</h4>
                        <select name="theme" id="theme" class="theme-toggle">
                            <option value="denvelope"'; if((isset($_COOKIE['theme']) && $_COOKIE['theme'] == "denvelope") || !isset($_COOKIE['theme'])) echo "selected"; echo'>Denvelope</option>
                            <option value="deep-koamaru"'; if(isset($_COOKIE['theme']) && $_COOKIE['theme'] == "deep-koamaru") echo "selected"; echo'>Deep Koamaru</option>
                            <option value="autumn"'; if(isset($_COOKIE['theme']) && $_COOKIE['theme'] == "autumn") echo "selected"; echo'>Autumn</option>
                            <option value="moonstone-blue"'; if(isset($_COOKIE['theme']) && $_COOKIE['theme'] == "moonstone-blue") echo "selected"; echo'>Moonstone Blue</option>
                        </select>
                    ';
                }
            ?>
            <hr class="account-settings-hr">
            <h4 class="account-settings-h4"><?php echo getTranslatedContent("settings_general_email_preferences"); ?></h4>
            <form action="../../php/update-preferences.php" method="post">
                <label for="on-new-logins" class="remember-me-container">
                    <input type="checkbox" name="on-new-logins" id="on-new-logins" class="email-preference-checkbox" <?php echo $emailPreferences['onNewLogins'] ? 'value="true" checked' : 'value="false"' ?>>
                    <span class="checkmark"></span>   
                    <?php echo getTranslatedContent("settings_general_email_preferences_on_new_logins"); ?>
                </label>
                <br>
                <button class="save-email-preferences-button" name="save-email-preferences-button" type="submit"><i class="fas fa-save"></i> <?php echo getTranslatedContent("settings_general_email_preferences_save"); ?></button>
            </form>
        </div>
        <div class="security-settings" id="security-settings">
            <form action="../../php/change-user-info.php" method="post">
                <h4 class="account-settings-h4"><?php echo getTranslatedContent("settings_security_change_password"); ?></h4>
                <input type="password" class="input" name="current-password" id="current-password" placeholder="<?php echo getTranslatedContent("settings_security_current_password"); ?>" style="width: 30%; border-radius: 5px;">
                <br>
                <br>
                <input type="password" class="input" name="new-password" id="new-password" placeholder="<?php echo getTranslatedContent("settings_security_new_password"); ?>" style="width: 30%; border-radius: 5px;">
                <br>
                <br>
                <button type="submit" class="change-pwd-button" id="change-pwd-button" name="change-password-button"><i class="fas fa-key"></i> <?php echo getTranslatedContent("settings_security_change_password"); ?></button>
            </form>
            <hr class="account-settings-hr">
            <?php /*echo '<h4 class="account-settings-h4" id="two-factor-auth-h4"><?php echo getTranslatedContent("settings_security_two_factor_authentication"); ?></h4>';*/ ?>
            <?php
                
                require(dirname(__FILE__, 3)."/php/has-2fa.php");

                // Disabled
                if(!has2FA() || true){
                    /*echo '<form action="../../authy-functions/add-user.php" method="post">
                            <select id="authy-countries" data-value="+'; echo $authyCountryPrefix; echo'"></select>
                            <input id="authy-cellphone" name="phone-number" class="input" type="text" value="" placeholder="'; echo getTranslatedContent("settings_security_two_factor_authentication_phone_number"); echo'"/>
                            <br><br>
                            <button type="submit" class="two-factor-auth-button" id="two-factor-auth-button" name="two-factor-auth-button">'; echo getTranslatedContent("settings_security_two_factor_authentication_enable"); echo'</button>
                        </form>
                    ';*/
                }
                else{
                    require(dirname(__FILE__, 3)."/php/get-2fa-user.php");

                    $twoFactorAuthUser = get2FAUser();
                    $phonePrefix = $twoFactorAuthUser['phonePrefix'];
                    $phoneNumber = $twoFactorAuthUser['phoneNumber'];

                    echo '<form action="../../authy-functions/remove-user.php" method="post">
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_security_two_factor_authentication_enabled_message"); echo'<br>(+'; echo $phonePrefix." ".$phoneNumber; echo')</h5>
                            <br>
                            <button type="submit" class="two-factor-auth-button" id="two-factor-auth-button-remove-button" name="two-factor-auth-remove-button">'; echo getTranslatedContent("settings_security_two_factor_authentication_remove_button"); echo'</button>
                        </form>
                    ';

                    //disabled but not to remove
                    /*echo '<label for="also-send-sms-authy" class="remember-me-container">
                            <input type="checkbox" name="also-send-sms-authy" id="also-send-sms-authy" class="also-send-sms-authy-checkbox" '; echo $twoFactorAuthUser['sendSMS'] == 1 ? "checked" : ""; echo'>
                            <span class="checkmark"></span>   
                            '; echo getTranslatedContent("settings_security_two_factor_authentication_also_send_sms"); echo'
                        </label>
                    ';*/
                }
                
            ?>
            <?php /*echo '<h5 class="account-settings-h4" style="font-size: 15px; margin: 0; margin-top: 2vw;"><i class="fas fa-exclamation-circle"></i> <?php echo getTranslatedContent("settings_security_two_factor_authentication_authy_app_needed_message"); ?></h5>';*/ ?>
            <?php
                /*
                $showStoreBadges = true;

                if($showStoreBadges && $isProduction){
                    echo '<div class="store-badges-container">
                            <div>
                                '; echo $GOOGLE_PLAY_AUTHY_APP_BADGE; echo'
                            </div>
                            <div>
                                '; echo $APP_STORE_AUTHY_APP_BADGE; echo'
                            </div>
                        </div>
                    ';
                }
                */
            ?>
            <?php /*echo '<hr class="account-settings-hr">';*/ ?>
            <form action="../../logout/" method="post">
                <h4 class="account-settings-h4"><?php echo getTranslatedContent("settings_security_logout_from_all_devices"); ?></h4>
                <button class="logout-from-all-button" id="logout-from-all-button" name="logout-from-all-button"><i class="fas fa-sign-out-alt"></i> <?php echo getTranslatedContent("settings_security_logout"); ?></button>
            </form>
            <hr class="account-settings-hr">
            <h4 class="account-settings-h4"><?php echo getTranslatedContent("settings_security_active_devices"); ?></h4>
            <div class="active-devices-container">
                <?php
                    require(dirname(__FILE__, 3)."/php/dbh.php");

                    $sqlQuery = "SELECT * FROM sessions WHERE sessionID=?";
                    $stmt = mysqli_stmt_init($conn);
            
                    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                        echo '';

                        exit();
                    }

                    mysqli_stmt_bind_param($stmt, "s", $GLOBALS['sessionID']);
                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);
                    $thisSession = mysqli_fetch_assoc($result);

                    if($thisSession){
                        echo '<div class="active-devices-div">
                                <div class="active-devices-icon">
                                    <i class="'; if($thisSession['OS'] != "unknown")if($thisSession['OS'] == "ios" || $thisSession['OS'] == "macos") echo $thisSession['OS']; else echo 'fab fa-'.$thisSession['OS'];else echo 'fas fa-question-circle'; echo'"></i>
                                    <span></span>
                                    <i class="'; if($thisSession['browser'] != "unknown")echo 'fab fa-'.$thisSession['browser'];else echo 'fas fa-question-circle'; echo'"></i>
                                </div>
                                <div class="active-devices-la-lo-div">
                                    <div class="active-devices-last-activity">
                                        <strong>'; echo getTranslatedContent("settings_security_last_activity"); echo':</strong><br><small>'; echo $thisSession['lastActivity']; echo'</small>
                                    </div>
                                    <div class="active-devices-location">
                                        <strong>'; echo getTranslatedContent("settings_security_location"); echo':</strong><br><small>'; echo $thisSession['location']; echo'</small>
                                    </div>
                                    <div style="width: 100%;">
                                        <button class="active-devices-this-button"><i class="fas fa-desktop"></i> '; echo getTranslatedContent("settings_security_this_device"); echo'</button>
                                    </div>
                                </div>
                            </div>
                        ';
                    }
                    else{
                        require(dirname(__FILE__, 3)."/php/get-os.php");
                        require(dirname(__FILE__, 3)."/php/get-browser.php");
                        require(dirname(__FILE__, 3)."/php/std-date.php");
                        require(dirname(__FILE__, 3)."/php/get-location.php");

                        $os = getOS();
                        $browser = getBrowser();
                        $location = getLocation($_SERVER['HTTP_X_FORWARDED_FOR']);

                        $location = $location['geo']['city'].", ".$location['geo']['country-name'];

                        echo '<div class="active-devices-div">
                                <div class="active-devices-icon">
                                    <i class="'; if($os != "unknown")if($os == "ios" || $os == "macos") echo $os; else echo 'fab fa-'.$os;else echo 'fas fa-question-circle'; echo'"></i>
                                    <span></span>
                                    <i class="'; if($browser != "unknown")echo 'fab fa-'.$browser;else echo 'fas fa-question-circle'; echo'"></i>
                                </div>
                                <div class="active-devices-la-lo-div">
                                    <div class="active-devices-last-activity">
                                        <strong>'; echo getTranslatedContent("settings_security_last_activity"); echo':</strong><br><small>'; echo stdDate(); echo'</small>
                                    </div>
                                    <div class="active-devices-location">
                                        <strong>'; echo getTranslatedContent("settings_security_location"); echo':</strong><br><small>'; echo $location; echo'</small>
                                    </div>
                                    <div style="width: 100%;">
                                        <button class="active-devices-this-button"><i class="fas fa-desktop"></i> '; echo getTranslatedContent("settings_security_this_device"); echo'</button>
                                    </div>
                                </div>
                            </div>
                        ';
                    }

                    $sqlQuery = "SELECT * FROM sessions WHERE username=? OR email=? ORDER BY unixTime DESC";
                    $stmt = mysqli_stmt_init($conn);
            
                    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                        echo '';

                        exit();
                    }

                    mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);

                    foreach ($result as $session) {
                        if($session['sessionID'] != $GLOBALS['sessionID']){
                            echo '<div class="active-devices-div">
                                    <div class="active-devices-icon">
                                        <i class="'; if($session['OS'] != "unknown")if($session['OS'] == "ios" || $session['OS'] == "macos") echo $session['OS']; else echo 'fab fa-'.$session['OS'];else echo 'fas fa-question-circle'; echo'"></i>
                                        <span></span>
                                        <i class="'; if($session['browser'] != "unknown")echo 'fab fa-'.$session['browser'];else echo 'fas fa-question-circle'; echo'"></i>
                                    </div>
                                    <div class="active-devices-la-lo-div">
                                        <div class="active-devices-last-activity">
                                            <strong>'; echo getTranslatedContent("settings_security_last_activity"); echo':</strong><br><small>'; echo $session['lastActivity']; echo'</small>
                                        </div>
                                        <div class="active-devices-location">
                                            <strong>'; echo getTranslatedContent("settings_security_location"); echo':</strong><br><small>'; echo $session['location']; echo'</small>
                                        </div>
                                        <div style="width: 100%;">
                                            <input type="hidden" name="id" value="'; echo $session['sessionLogoutID']; echo'">
                                            <button type="submit" name="active-devices-logout-button" class="active-devices-logout-button" id="active-devices-logout-button"><i class="fas fa-sign-out-alt"></i> '; echo getTranslatedContent("settings_security_logout"); echo'</button>
                                        </div>
                                    </div>
                                </div>
                            ';
                        }
                    }
                ?>
            </div>
        </div>
        <div class="advanced-settings" id="advanced-settings">
            <form action="../../php/delete-account.php" method="post">
                <h4 class="account-settings-h4"><?php echo getTranslatedContent("settings_advanced_delete_account"); ?></h4>
                <button type="submit" class="delete-account-button" name="delete-account-button" id="delete-account-button"><i class="fas fa-trash"></i> <?php echo getTranslatedContent("settings_advanced_delete"); ?></button>
            </form>
            <p class="account-settings-h5 account-settings-privacy-disclaimer" id="delete-account-p" style="display: none;"><?php echo getTranslatedContent("settings_advanced_before_delete_message"); ?></p>
        </div>
        <div class="info-settings" id="info-settings">
            <?php
                require(dirname(__FILE__, 3)."/php/dbh.php");
                require(dirname(__FILE__, 3)."/php/better-size.php");

                $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    echo '';

                    exit();
                }

                mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);

                $sqlQuery = "SELECT * FROM files WHERE usernameAuthor=? OR emailAuthor=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    echo '';

                    exit();
                }

                mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                mysqli_stmt_execute($stmt);

                $files = mysqli_stmt_get_result($stmt);

                $numOfFiles = 0;
                $averageFileSize = "0";
                $smallestFileSize = "0";
                $biggestFileSize = "0";
                $filesUploadedThisWeek = 0;

                $lastMondayUnixTime = strtotime("last Monday");
                
                $i = 0;

                foreach ($files as $file) {
                    if($i == 0){
                        $smallestFileSize = $file['sizeInBytes'];
                        $biggestFileSize = $file['sizeInBytes'];
                    }

                    if($file['sizeInBytes'] < $smallestFileSize){
                        $smallestFileSize = $file['sizeInBytes'];
                    }
                    else if($file['sizeInBytes'] > $biggestFileSize){
                        $biggestFileSize = $file['sizeInBytes'];
                    }

                    if($file['unixTimeCreated'] >= $lastMondayUnixTime){
                        $filesUploadedThisWeek++;
                    }

                    $averageFileSize += $file['sizeInBytes'];

                    $numOfFiles++;
                    $i++;
                }

                if($i == 0){
                    $averageFileSize = "0B";
                }
                else{
                    $averageFileSize = betterSize($averageFileSize / $numOfFiles);
                }

                $smallestFileSize = betterSize($smallestFileSize);
                $biggestFileSize = betterSize($biggestFileSize);

                echo '<h4 class="account-settings-h4">'; echo getTranslatedContent("settings_info_account_info"); echo'</h4>
                    <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_info_account_created"); echo': <small>'; echo $user['created']; echo'</small></h5>
                    <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_info_files_uploaded_since"); echo' '; echo explode(" ", $user['created'])[0]; echo': <small>'; echo $user['filesUploaded']; echo'</small></h5>
                    <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_info_files_currently_uploaded"); echo': <small>'; echo $numOfFiles; echo'</small></h5>
                    <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_info_average_file_size"); echo': <small>'; echo $averageFileSize; echo'</small></h5>
                    <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_info_smallest_file_currently_uploaded"); echo': <small>'; echo $smallestFileSize; echo'</small></h5>
                    <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_info_biggest_file_currently_uploaded"); echo': <small>'; echo $biggestFileSize; echo'</small></h5>
                    <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_info_files_uploaded_this_week"); echo': <small>'; echo $filesUploadedThisWeek; echo'</small></h5>
                    <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_info_files_edited_after_uploading_since"); echo' '; echo explode(" ", $user['created'])[0]; echo': <small>'; echo $user['filesEditedAfterUpload']; echo'</small></h5>
                    <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_info_used_storage"); echo': <small>'; echo $user['usedStorage']." / ".$user['maxStorage']; echo'</small></h5>
                ';
            ?>

            <?php
                if(!$betaDisableAccess){
                    require(dirname(__FILE__, 3)."/php/get-elastic-beanstalk-ver.php");
                    require(dirname(__FILE__, 3)."/php/get-changelog.php");

                    if($isProduction){
                        $ver = getEBVer();
                        $changelog = getChangelog($ver);
                    }

                    echo '<div id="tos-pp-cp-cu-settings">
                            <hr class="account-settings-hr">
                            <h4 class="account-settings-h4">'; echo getTranslatedContent("settings_info_terms_of_service"); echo'</h4>
                            <a href="../../terms" target="_blank"><i class="fas fa-balance-scale"></i> '; echo getTranslatedContent("settings_info_terms_of_service"); echo'</a>
                            <hr class="account-settings-hr">
                            <h4 class="account-settings-h4">'; echo getTranslatedContent("settings_info_privacy_policy"); echo'</h4>
                            <a href="../../privacy" target="_blank"><i class="fas fa-user-shield"></i> '; echo getTranslatedContent("settings_info_privacy_policy"); echo'</a>
                            <hr class="account-settings-hr">
                            <h4 class="account-settings-h4">'; echo getTranslatedContent("settings_info_cookie_policy"); echo'</h4>
                            <a href="../../cookies" target="_blank"><i class="fas fa-cookie"></i> '; echo getTranslatedContent("settings_info_cookie_policy"); echo'</a>
                            <hr class="account-settings-hr">
                            <h4 class="account-settings-h4">'; echo getTranslatedContent("settings_info_contact_us"); echo'</h4>
                            <a href="../../contact" target="_blank"><i class="fas fa-envelope"></i> '; echo getTranslatedContent("settings_info_contact_us"); echo'</a>
                            ';
                            if($isProduction){
                                echo '<hr class="account-settings-hr">
                                    <h4 class="account-settings-h4 account-settings-h5" style="margin-bottom: 0;">'; echo getTranslatedContent("settings_info_app_ver").": ".$ver; echo'</h4>
                                ';

                                echo '<hr class="account-settings-hr">
                                    <h4 class="account-settings-h4">'; echo getTranslatedContent("settings_info_latest_changelog"); echo'</h4>
                                ';

                                echo '<ul class="changelog-ul">';
                                foreach ($changelog as $log) {
                                    echo "<li>".$log."</li>";
                                }
                                echo "</ul>";
                            }
                            echo'
                            <hr class="account-settings-hr">
                            <a style="display: block;" id="powered-by-aws" target="_blank" rel="noopener" href="https://aws.amazon.com/what-is-cloud-computing"><img src="https://d0.awsstatic.com/logos/powered-by-aws-white.png" alt="Powered by AWS Cloud Computing"></a>
                        </div>
                    ';
                }
            ?>
        </div>
        <?php
            if($enablePayments){
                echo '<style>
                        .plan-div-current p:first-child::after{
                            content: " ('; echo getTranslatedContent("settings_plan_current"); echo')";
                        }
                    </style>
                ';
                echo '<div class="plan-settings" id="plan-settings">
                <h4 class="account-settings-h4" id="plan-h4" '; echo $_SESSION['plan'] != "Free" ? "style=\"margin-bottom: 0\"" : ""; echo'>'; echo getTranslatedContent("settings_plan_title"); echo'</h4>
                ';
                if($_SESSION['plan'] != "Free"){
                    require(dirname(__FILE__, 3)."/php/get-customer.php");
                    $nextRenewal = getCustomer()['nextRenewal'];

                    if(strpos($nextRenewal, "cancelled") === false && strpos($nextRenewal, "downgraded") === false){
                        echo '<p class="next-renewal">'; echo getTranslatedContent("settings_plan_next_renewal"); echo': '; echo $nextRenewal; echo'</p>';
                    }
                    else if(strpos($nextRenewal, "downgraded") > -1){
                        echo '<p class="next-renewal">'; echo getTranslatedContent("settings_plan_subscritption_downgraded"); echo'<br>'; echo getTranslatedContent("settings_plan_subscritption_downgraded_new_plan"); echo': '; echo "<span style='font-weight: var(--font-weight-bold);'>".explode("||", $nextRenewal)[1]."</span> "; echo getTranslatedContent("settings_plan_subscritption_downgraded_new_plan_from")." "; echo "<span style='font-weight: var(--font-weight-bold);'>".explode("||", $nextRenewal)[2]."</span>"; echo'</p>';
                    }
                    else{
                        echo '<p class="next-renewal">'; echo getTranslatedContent("settings_plan_subscritption_canceled"); echo'<br>'; echo getTranslatedContent("settings_plan_subscritption_canceled_active_until"); echo': '; echo explode("||", $nextRenewal)[1]; echo'</p>';
                    }
                }
                echo'
                <p id="disclaimer-plan-choice">'; echo getTranslatedContent("settings_plan_downgrade_disclaimer"); echo'</p>
                <form action="../../charge/" method="post" class="payment-form" id="'; if($_SESSION['plan'] == "Free") echo "payment-form"; echo'">
                    <div class="plan-container">
                        <div class="plan-div '; if($_SESSION['plan'] == "Free") echo "plan-div-selected plan-div-current"; echo'">
                            <p>'; echo getTranslatedContent("settings_plan_tier_free"); echo'</p>
                            <p>'; echo $FREE_TIER_PRICE.$currency; echo' / '; echo getTranslatedContent("settings_plan_month"); echo'</p>
                            <p>'; echo $FREE_TIER_STORAGE; echo' '; echo getTranslatedContent("settings_plan_storage"); echo'</p>
                        </div>
                        <div class="plan-div '; if($_SESSION['plan'] == "Personal") echo "plan-div-selected plan-div-current"; echo'">
                            <p>'; echo getTranslatedContent("settings_plan_tier_personal"); echo'</p>
                            <p>'; echo $PERSONAL_TIER_PRICE.$currency; echo' / '; echo getTranslatedContent("settings_plan_month"); echo'</p>
                            <p>'; echo $PERSONAL_TIER_STORAGE; echo' '; echo getTranslatedContent("settings_plan_storage"); echo'</p>
                        </div>
                        <div class="plan-div '; if($_SESSION['plan'] == "Personal Plus") echo "plan-div-selected plan-div-current"; echo'">
                            <p>'; echo getTranslatedContent("settings_plan_tier_personal_plus"); echo'</p>
                            <p>'; echo $PERSONAL_PLUS_TIER_PRICE.$currency; echo' / '; echo getTranslatedContent("settings_plan_month"); echo'</p>
                            <p>'; echo $PERSONAL_PLUS_TIER_STORAGE; echo' '; echo getTranslatedContent("settings_plan_storage"); echo'</p>
                        </div>
                        <div class="plan-div '; if($_SESSION['plan'] == "Professional") echo "plan-div-selected plan-div-current"; echo'">
                            <p>'; echo getTranslatedContent("settings_plan_tier_professional"); echo'</p>
                            <p>'; echo $PROFESSIONAL_TIER_PRICE.$currency; echo' / '; echo getTranslatedContent("settings_plan_month"); echo'</p>
                            <p>'; echo $PROFESSIONAL_TIER_STORAGE; echo' '; echo getTranslatedContent("settings_plan_storage"); echo'</p>
                        </div>
                        <div class="plan-div '; if($_SESSION['plan'] == "Professional Plus") echo "plan-div-selected plan-div-current"; echo'">
                            <p>'; echo getTranslatedContent("settings_plan_tier_professional_plus"); echo'</p>
                            <p>'; echo $PROFESSIONAL_PLUS_TIER_PRICE.$currency; echo' / '; echo getTranslatedContent("settings_plan_month"); echo'</p>
                            <p>'; echo $PROFESSIONAL_PLUS_TIER_STORAGE; echo' '; echo getTranslatedContent("settings_plan_storage"); echo'</p>
                        </div>
                        <div class="plan-div '; if($_SESSION['plan'] == "Enterprise") echo "plan-div-selected plan-div-current"; echo'">
                            <p>'; echo getTranslatedContent("settings_plan_tier_enterprise"); echo'</p>
                            <p>'; echo $ENTERPRISE_TIER_PRICE.$currency; echo' / '; echo getTranslatedContent("settings_plan_month"); echo'</p>
                            <p>'; echo $ENTERPRISE_TIER_STORAGE; echo' '; echo getTranslatedContent("settings_plan_storage"); echo'</p>
                        </div>
                        ';
                        if(strpos($_SESSION['plan'], "Custom") > -1){
                            echo '<div style="width: 100%;" class="plan-div plan-div-selected plan-div-current">
                                    <p>'; echo getTranslatedContent("settings_plan_tier_custom"); echo'</p>
                                    <p>'; echo explode("||", $_SESSION['plan'])[2].$currency; echo' / '; echo getTranslatedContent("settings_plan_month"); echo'</p>
                                    <p>'; echo explode("||", $_SESSION['plan'])[1]; echo' '; echo getTranslatedContent("settings_plan_storage"); echo'</p>
                                </div>
                            ';
                        }
                        echo'
                        <a href="../../contact" class="need-more-container-link">
                            <div style="width: 100%;" class="plan-div '; echo'">
                                <p style="margin: 0;">'; echo getTranslatedContent("settings_plan_need_more"); echo'</p>
                            </div>
                        </a>
                    </div>';
                    
                        if($_SESSION['plan'] == "Free"){
                            echo '<div class="form-row">
                                    <label class="account-settings-h5" for="card-element">'; echo getTranslatedContent("settings_plan_credit_or_debit_card"); echo'</label>
                                    <div id="card-element" style="margin-top: 1rem;"></div>
                                    <div class="supported-payment-methods">
                                        <abbr title="Visa"><i class="pf pf-visa"></i></abbr>
                                        <abbr title="Visa Debit"><i class="pf pf-visa-debit"></i></abbr>
                                        <abbr title="Mastercard"><i class="pf pf-mastercard"></i></abbr>
                                        <abbr title="Maestro"><i class="pf pf-maestro"></i></abbr>
                                        <abbr title="American Express"><i class="pf pf-american-express"></i></abbr>
                                    </div>
                                    <div id="card-errors" role="alert"></div>
                                </div>
                            ';
                        }
                    echo '
                    <input type="hidden" id="selected-plan" name="selected-plan" value="'; echo $_SESSION['plan']; echo'">';
                    
                        if($_SESSION['plan'] == "Free"){
                            echo '<button class="change-plan-button" name="change-plan-button">'; echo getTranslatedContent("settings_plan_upgrade_plan"); echo'</button>';
                        }
                        else{
                            echo '<button class="change-plan-button" name="change-plan-button">'; echo getTranslatedContent("settings_plan_change_plan"); echo'</button>';
                        }
                    echo '
                </form>';
                
                    if($_SESSION['plan'] != "Free"){
                        echo '<form action="../../charge/" method="post" id="cancel-plan-form">
                                <button class="change-plan-button cancel-plan-button" name="cancel-plan-button" id="cancel-plan-button">'; echo getTranslatedContent("settings_plan_cancel_plan"); echo'</button>
                            </form>
                        ';
                    }
                
                
                    if($_SESSION['plan'] != "Free"){
    
                        require(dirname(__FILE__, 3)."/php/dbh.php");
    
                        $sqlQuery = "SELECT * FROM customers WHERE username=? OR email=?";
                        $stmt = mysqli_stmt_init($conn);
    
                        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                            echo '';
        
                            exit();
                        }
        
                        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                        mysqli_stmt_execute($stmt);
        
                        $result = mysqli_stmt_get_result($stmt);
                        $customer = mysqli_fetch_assoc($result);
    
                        echo '<hr class="account-settings-hr">
                            <div id="cc-info-div">
                                <h4 class="account-settings-h4">'; echo getTranslatedContent("settings_plan_card"); echo'</h4>
                                <div>
                                    <h5 class="account-settings-h5 account-settings-cc"><span>'; echo $customer['cardIcon']."</span><div style='display: flex; justify-content: space-evenly; width: 100%;'><span>".$customer['lastFourDigits']."</span><span>".$customer['expirationDate']."</span></div>"; echo'</h5>
                                    <h5 class="account-settings-h5 account-settings-cc-next">'; echo getTranslatedContent("settings_plan_next_billing_cycle"); echo': <span>'; echo $customer['nextRenewal']; echo'</span></h5>
                                </div>
                                <button class="change-cc-button" id="change-cc-button" style="margin-top: 2%;"><i class="fas fa-credit-card"></i> '; echo getTranslatedContent("settings_plan_change_card"); echo'</button>
                            </div>
                            <form action="../../charge/" method="post" class="payment-form" id="payment-form" style="display: none; margin-bottom: 5vw;">
                                <div class="form-row">
                                    <label class="account-settings-h5" for="card-element">'; echo getTranslatedContent("settings_plan_credit_or_debit_card"); echo'</label>
                                    <div id="card-element"></div>
                                    <div id="card-errors" role="alert"></div>
                                </div>
                                <input type="hidden" name="change-cc-button-confirm">
                                <button class="change-cc-button-confirm" name="change-cc-button-confirm">'; echo getTranslatedContent("settings_plan_change_card"); echo'</button>
                            </form>
                        ';
                    }
                echo '
                <hr class="account-settings-hr">
                <div>
                    <h4 class="account-settings-h4">'; echo getTranslatedContent("settings_plan_faq"); echo'</h4>
                    <div class="faq-div">
                        <div>
                            <h5>'; echo getTranslatedContent("settings_plan_faq_storage_space_only_difference"); echo'</h5>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <p>'; echo getTranslatedContent("settings_plan_faq_storage_space_only_difference_answer"); echo'</p>
                    </div>
                </div>
            </div>';
            }
        ?>
        <div class="privacy-settings" id="privacy-settings">
            <h4 class="account-settings-h4"><?php echo getTranslatedContent("settings_privacy_logs"); ?></h4>
            <?php
                if(/*$GLOBALS['user']['optOutLogCollection'] == 1*/ false){
                    echo '<h5 class="account-settings-h5 account-settings-privacy-disclaimer">
                            You have opted-out from log collection
                        </h5>
                        <form action="../../php/opt-in-logs.php" method="post">
                            <button type="submit" name="opt-in-logs-button" class="delete-logs-button" style="margin-top: 1vw;"><i class="fas fa-clipboard-list"></i> Enable Log Collection</button>
                        </form>
                    ';
                }
                else{
                    require(dirname(__FILE__, 3)."/php/dbh.php");

                    $sqlQuery = "SELECT * FROM logs WHERE username=? OR email=?";
                    $stmt = mysqli_stmt_init($conn);
    
                    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                        echo '';
    
                        exit();
                    }
    
                    mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                    mysqli_stmt_execute($stmt);
        
                    $logs = mysqli_stmt_get_result($stmt);
    
                    $SIGNUP = 0;
                    $LOGIN_COOKIE = 0;
                    $LOGIN_NO_COOKIE = 0;
                    $LOGOUT_COOKIE = 0;
                    $LOGOUT_NO_COOKIE = 0;
                    $LOGOUT_ACCOUNT_SECURITY = 0;
                    $LOGOUT_FROM_ALL = 0;
                    $USERNAME_CHANGE = 0;
                    $EMAIL_CHANGE = 0;
                    $PASSWORD_CHANGE = 0;
                    $SINGLE_FILE_UPLOAD = 0;
                    $PROJECT_FOLDER_UPLOAD_CREATED = 0;
                    $PROJECT_FILE_UPLOAD = 0;
                    $MULTIPLE_FILES_FILE_UPLOAD = 0;
                    $NEW_FILE_CREATED = 0;
                    $NEW_FOLDER_CREATED = 0;
                    $FILE_DELETED = 0;
                    $FOLDER_DELETED = 0;
                    $FOLDER_DELETED_FROM_PARENT_FOLDER_DELETE = 0;
                    $FILES_DELETED_FROM_PARENT_FOLDER_DELETE = 0;
                    $LOGOUT_ACCOUNT_SECURITY_NEW_LOGIN_EMAIL = 0;
    
                    foreach ($logs as $log) {
                        switch ($log['logType']) {
                            case 'SIGNUP':
                                $SIGNUP++;
                                break;
                            case 'LOGIN_COOKIE':
                                $LOGIN_COOKIE++;
                                break;
                            case 'LOGIN_NO_COOKIE':
                                $LOGIN_NO_COOKIE++;
                                break;
                            case 'LOGOUT_COOKIE':
                                $LOGOUT_COOKIE++;
                                break;
                            case 'LOGOUT_NO_COOKIE':
                                $LOGOUT_NO_COOKIE++;
                                break;
                            case 'LOGOUT_ACCOUNT_SECURITY':
                                $LOGOUT_ACCOUNT_SECURITY++;
                                break;
                            case 'LOGOUT_FROM_ALL':
                                $LOGOUT_FROM_ALL++;
                                break;
                            case 'USERNAME_CHANGE':
                                $USERNAME_CHANGE++;
                                break;
                            case 'EMAIL_CHANGE':
                                $EMAIL_CHANGE++;
                                break;
                            case 'PASSWORD_CHANGE':
                                $PASSWORD_CHANGE++;
                                break;
                            case 'SINGLE_FILE_UPLOAD':
                                $SINGLE_FILE_UPLOAD++;
                                break;
                            case 'PROJECT_FOLDER_UPLOAD_CREATED':
                                $PROJECT_FOLDER_UPLOAD_CREATED++;
                                break;
                            case 'PROJECT_FILE_UPLOAD':
                                $PROJECT_FILE_UPLOAD++;
                                break;
                            case 'MULTIPLE_FILES_FILE_UPLOAD':
                                $MULTIPLE_FILES_FILE_UPLOAD++;
                                break;
                            case 'NEW_FILE_CREATED':
                                $NEW_FILE_CREATED++;
                                break;
                            case 'NEW_FOLDER_CREATED':
                                $NEW_FOLDER_CREATED++;
                                break;
                            case 'FILE_DELETED':
                                $FILE_DELETED++;
                                break;
                            case 'FOLDER_DELETED':
                                $FOLDER_DELETED++;
                                break;
                            case 'FOLDER_DELETED_FROM_PARENT_FOLDER_DELETE':
                                $FOLDER_DELETED_FROM_PARENT_FOLDER_DELETE++;
                                break;
                            case strpos($log['logType'], 'FILES_DELETED_FROM_PARENT_FOLDER_DELETE'):
                                $FILES_DELETED_FROM_PARENT_FOLDER_DELETE += substr($log['logType'], strpos($log['logType'], '*') + 1);
                                break;
                            case 'LOGOUT_ACCOUNT_SECURITY_NEW_LOGIN_EMAIL':
                                $LOGOUT_ACCOUNT_SECURITY_NEW_LOGIN_EMAIL++;    
                                break;
                            default:
                                break;
                        }
                    }
    
                    echo '
                        <div>
                            ';/*<h5 class="account-settings-h4 account-settings-h5">SIGNUP: <small>'; echo $SIGNUP; echo'</small></h5>*/ echo'
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_login"); echo': <small>'; echo $LOGIN_COOKIE + $LOGIN_NO_COOKIE; echo'</small></h5>
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_logout"); echo': <small>'; echo $LOGOUT_COOKIE + $LOGIN_NO_COOKIE + $LOGOUT_ACCOUNT_SECURITY + $LOGOUT_ACCOUNT_SECURITY_NEW_LOGIN_EMAIL + $LOGOUT_FROM_ALL; echo'</small></h5>
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_username_changes"); echo': <small>'; echo $USERNAME_CHANGE; echo'</small></h5>
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_email_changes"); echo': <small>'; echo $EMAIL_CHANGE; echo'</small></h5>
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_password_changes"); echo': <small>'; echo $PASSWORD_CHANGE; echo'</small></h5>
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_files_uploaded"); echo': <small>'; echo $SINGLE_FILE_UPLOAD + $PROJECT_FILE_UPLOAD + $MULTIPLE_FILES_FILE_UPLOAD; echo'</small></h5>
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_folders_uploaded"); echo': <small>'; echo $PROJECT_FOLDER_UPLOAD_CREATED; echo'</small></h5>
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_files_created"); echo': <small>'; echo $NEW_FILE_CREATED; echo'</small></h5>
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_folders_created"); echo': <small>'; echo $NEW_FOLDER_CREATED; echo'</small></h5>
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_files_deleted"); echo': <small>'; echo $FILE_DELETED + $FILES_DELETED_FROM_PARENT_FOLDER_DELETE; echo'</small></h5>
                            <h5 class="account-settings-h4 account-settings-h5">'; echo getTranslatedContent("settings_privacy_logs_folders_deleted"); echo': <small>'; echo $FOLDER_DELETED + $FOLDER_DELETED_FROM_PARENT_FOLDER_DELETE; echo'</small></h5>
                        </div>
                    ';

                    echo '<br>
                    <form action="../../php/delete-logs.php" method="post">
                        <label for="opt-out-logs" class="remember-me-container">
                            <input type="checkbox" name="opt-out-logs" id="opt-out-logs"'; if($GLOBALS["user"]["optOutLogCollection"] == 1) echo "value='true' checked"; else echo "value='false'"; echo'>
                            <span class="checkmark"></span>   
                            '; echo getTranslatedContent("settings_privacy_opt_out_logs"); echo'
                        </label>
                        <button type="submit" name="delete-logs-button" class="delete-logs-button" style="margin-top: 1vw;"><i class="fas fa-trash"></i> '; echo getTranslatedContent("settings_privacy_delete_logs"); echo'</button>
                    </form>
                    <h5 class="account-settings-h5 account-settings-privacy-disclaimer">'; echo getTranslatedContent("settings_privacy_disclaimer"); echo'</h5>';
                }
            ?>
        </div>
        
        <div class="support-settings" id="support-settings">
            <div id="support-settings-main">
                <h4 class="account-settings-h4"><?php echo getTranslatedContent("settings_support_my_cases"); ?></h4>
                <?php
                    require(dirname(__FILE__, 3)."/php/dbh.php");
                    require(dirname(__FILE__, 3)."/php/get-support-cases.php");

                    $supportCases = getSupportCasesByUser();

                    $i = 0;

                    echo '<div class="support-cases-container">';

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
                        echo '<div class="empty-folder-div" id="empty-folder-div">
                                <h3 style="margin-bottom: 0;" id="empty-folder-div-h3">'; echo getTranslatedContent("support_center_no_open_support_cases"); echo'</h3>
                                <img src="../../img/humaaans/sitting-2.svg" alt="" style="max-width: 100%;" id="empty-folder-div-img">
                                <a class="contact-us-no-open-support-cases" href="../../contact" target="_blank"><i class="fas fa-envelope"></i> '; echo getTranslatedContent("settings_info_contact_us"); echo'</a>
                            </div>
                        ';
                    }

                    echo '</div>';
                ?>
            </div>
            <button class="support-cases-settings-back-button" id="support-cases-settings-back-button"><i class="fas fa-arrow-left"></i> <?php echo getTranslatedContent("account_back"); ?></button>
            <div id="support-settings-chat-history">
                <div class="support-messages-container" id="support-messages-container"></div>
                <div class="support-message-bar-respond" id="support-message-bar-respond">
                    <form action="../../php/user-reply.php" method="post">
                        <input type="hidden" name="case-number" id="case-number">
                        <div class="message-bottom-bar">
                                <p class="error-message" id="error-message-reply-empty"><?php echo getTranslatedContent("support_center_error_reply_message_empty"); ?></p>
                                <p class="error-message" id="error-message-reply-too-long"><?php echo getTranslatedContent("support_center_error_reply_message_too_long"); ?></p>
                            <div class="message-bottom-bar-input-button-container">
                                    <div class="message-bottom-bar-input">
                                        <input type="text" name="message" id="message" class="input" placeholder="<?php echo getTranslatedContent("support_center_message"); ?>" maxlength="5000">
                                    </div>
                                    <div class="message-bottom-bar-send-button">
                                        <button type="submit" name="send-reply-button" id="send-reply-button"><i class="fas fa-paper-plane"></i></button>
                                    </div>
                            </div>
                            <div class="counter-label-container">
                                <p class="char-counter-support-message"><span>0</span> / 5000</p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="spinner">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>

    <script>
        var firstButtonClickDeleteAccount = true;
        var firstButtonClickCancelPlan = true;

        $(document).ready(function(){
            var username = $("#username-account-settings").val();
            var email = $("#email-account-settings").val();
            var patternUsername = /^[a-zA-Z0-9\.\-_ ]*$/;
            var patternEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            var validUsername = false;
            var validEmail = false;

            var url = window.location.href;
            var anchor = url.split("#")[1];
            
            switch (anchor) {
                case "general":
                    general(true);
                    break;
                case "security":
                    security(true);
                    break;
                case "advanced":
                    advanced(true);
                    break;
                case "info":
                    info(true);
                    break;
                <?php
                    if($enablePayments){
                        echo 'case "plan":
                                plan(true);
                                break;
                        ';
                    }
                ?>
                case "privacy":
                    privacy(true);
                    break;
                case "support":
                    support(true);
                    break;
                default:
                    break;
            }

            $("#account-settings-arrow-down-mob").click(function(){
                $("#account-settings-arrow-down-mob").toggleClass("rotate");
                
                if($(".account-settings-div-mob").css("display") == "none"){
                    $(".account-settings-div-mob").css("display", "block");
                    $(".account-settings-div-mob").fadeTo(100, 1, "linear");
                }
                else{
                    $(".account-settings-div-mob").fadeTo(100, 0, "linear", function(){
                        $(".account-settings-div-mob").css("display", "none");
                    });
                }
            });

            $("#username-account-settings").keyup(function(){
                username = $("#username-account-settings").val();
                validUsername = checkUsername();
                if(validUsername){
                    $("#account-settings-change-button-username").css("display", "block");
                    $("#username-account-settings").css("border-bottom-left-radius", "0px");
                    $("#username-edit-button").css("border-bottom-right-radius", "0px");
                }
                else{
                    $("#account-settings-change-button-username").css("display", "none");
                    $("#username-account-settings").css("border-bottom-left-radius", "5px");
                    $("#username-edit-button").css("border-bottom-right-radius", "5px");
                }
            });

            $("#email-account-settings").keyup(function(){
                email = $("#email-account-settings").val();
                validEmail = checkEmail();
                if(validEmail){
                    $("#account-settings-change-button-email").css("display", "block");
                    $("#email-account-settings").css("border-bottom-left-radius", "0px");
                    $("#email-edit-button").css("border-bottom-right-radius", "0px");
                }
                else{
                    $("#account-settings-change-button-email").css("display", "none");
                    $("#email-account-settings").css("border-bottom-left-radius", "5px");
                    $("#email-edit-button").css("border-bottom-right-radius", "5px");
                }
            });

            function checkUsername(){
                if(username.length >= 4 && username.length <= 255){
                    if(patternUsername.test(username)){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return false;
                }
            }

            function checkEmail(){
                if(email.length <= 255){
                    if(patternEmail.test(email)){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return false;
                }
            }

            $("#username-edit-button").click(function(){
                $("#username-account-settings").toggleClass("input-disabled");

                if($("#account-settings-change-button-username").css("display") == "block"){
                    $("#account-settings-change-button-username").css("display", "none");
                    $("#username-account-settings").css("border-bottom-left-radius", "5px");
                    $("#username-edit-button").css("border-bottom-right-radius", "5px");
                }

                $("#username-account-settings").val("<?php echo $_SESSION['username'] ?>");
            });
            $("#email-edit-button").click(function(){
                $("#email-account-settings").toggleClass("input-disabled");

                if($("#account-settings-change-button-email").css("display") == "block"){
                    $("#account-settings-change-button-email").css("display", "none");
                    $("#email-account-settings").css("border-bottom-left-radius", "5px");
                    $("#email-edit-button").css("border-bottom-right-radius", "5px");
                }

                $("#email-account-settings").val("<?php echo $_SESSION['email'] ?>");
            });

            <?php
                if($enablePayments){
                    echo '$(".plan-div").click(function(){
                            var currentPlan = "'; echo $_SESSION['plan']; echo'";
                            $(".plan-div-selected").removeClass("plan-div-selected");
                            $(this).addClass("plan-div-selected");
                            var plan = $(this).children("p:first-child").html();
                            $("#selected-plan").val(plan);
            
                            if(planIndex(currentPlan) > planIndex(plan)){
                                $("#disclaimer-plan-choice").css("display", "block");
                                $("#plan-h4").css("margin", "0");
                            }
                            else{
                                $("#disclaimer-plan-choice").css("display", "none");
                                '; 
                                if($_SESSION['plan'] == "Free"){
                                    echo '$("#plan-h4").css("margin-bottom", "2vw");';
                                }
                                echo'
                            }
                        });
            
                        $("#change-cc-button").click(function(){
                            $("#payment-form").css("display", "block");
                            $("#cc-info-div").css("margin-bottom", "1vw");
                        });
                    ';
                }
            ?>

            $("#opt-out-logs").click(function(){
                var optIn = false;

                if($("#opt-out-logs").is(":checked")){
                    $("#opt-out-logs").val("true");
                }
                else{
                    $("#opt-out-logs").val("false");
                    optIn = true;
                }

                $.ajax({
                    type: "POST",
                    url: "../../php/opt-out-log.php",
                    data: "opt-in=" + optIn,
                    dataType: "JSON",
                    success: function(r){
                        if(r[0]['optedOut'] == true){
                            $("#success-msg").css("display", "block");
                            setTimeout(function(){
                                $("#success-msg").css("display", "none")
                            }, 2500);
                        }
                    },
                    error: function(r){
                        $("#error-msg").css("display", "block");
                        setTimeout(function(){
                            $("#error-msg").css("display", "none")
                        }, 2500);
                    }
                });
            });

            $(".email-preference-checkbox").click(function(){
                if($(this).is(":checked")){
                    $(this).val("true");
                }
                else{
                    $(this).val("false");
                }
            });

            $("#delete-account-button").click(function(e){
                if(firstButtonClickDeleteAccount == true){
                    e.preventDefault();
                    firstButtonClickDeleteAccount = false;
                    $("#delete-account-p").css("display", "block");
                }
            });

            $("#cancel-plan-button").click(function(e){
                if(firstButtonClickCancelPlan == true){
                    e.preventDefault();
                    firstButtonClickCancelPlan = false;
                    $("#disclaimer-plan-choice").append("<br class='cancel-plan-button-disclaimer-elements'><br class='cancel-plan-button-disclaimer-elements'><span class='cancel-plan-button-disclaimer-elements'><?php echo getTranslatedContent("settings_plan_cancel_confirm_message"); ?></span>");
                    $("#disclaimer-plan-choice").css("display", "block");
                }
            });

            $(".faq-div").click(function(){
                if($(this).children("p").css("display") == "none"){
                    $(this).children("div").children("i").addClass("rotate");
                    $(this).children("p").css("display", "block");
                }
                else{
                    $(this).children("div").children("i").removeClass("rotate");
                    $(this).children("p").css("display", "none");
                }
            });

            $("#language").change(function(){
                var language = $("#language")[0][$("#language")[0].selectedIndex].value;
            
                $.ajax({
                    type: "POST",
                    url: "../../php/change-language.php",
                    data: "language=" + language,
                    dataType: "JSON",
                    success: function(r){
                        if(r[0]['languageUpdated'] == true){
                            location.reload();
                        }
                        else if(r[0]['languageUpdated'] == false){
                            $("#error-msg").css("display", "block");
                            setTimeout(function(){
                                $("#error-msg").css("display", "none")
                            }, 2500);
                        }
                    },
                    error: function(r){
                        $("#error-msg").css("display", "block");
                        setTimeout(function(){
                            $("#error-msg").css("display", "none")
                        }, 2500);
                    }
                });
            });

            <?php
                /* disabled but not to remove
                echo '$("#also-send-sms-authy").click(function(){
                        var sendSMS = false;
        
                        if($("#also-send-sms-authy").is(":checked")){
                            sendSMS = true;
                        }
        
                        $.ajax({
                            type: "POST",
                            url: "../../authy-functions/update-sms-preference.php",
                            data: "send-sms=" + sendSMS,
                            dataType: "JSON",
                            success: function(r){
                                if(r[0][\'SMSPreferenceUpdated\'] == true){
                                    $("#success-msg").css("display", "block");
                                    setTimeout(function(){
                                        $("#success-msg").css("display", "none")
                                    }, 2500);
                                }
                            },
                            error: function(r){
                                $("#error-msg").css("display", "block");
                                setTimeout(function(){
                                    $("#error-msg").css("display", "none")
                                }, 2500);
                            }
                        });
                    });
                ';
                */
            ?>

            $(".active-devices-logout-button").click(function(){
                var sessionLogoutID = $(this).prev().val();

                $.ajax({
                    type: "POST",
                    url: "../../logout/",
                    data: "device-logout-id=" + sessionLogoutID,
                    dataType: "JSON",
                    success: function(r){
                        if(r[0]['activeDeviceLogoutSuccess'] == true){
                            $("#success-msg").css("display", "block");
                            setTimeout(function(){
                                $("#success-msg").css("display", "none")
                            }, 2500);

                            $("input[value=" + sessionLogoutID + "]").parent().parent().parent().remove();
                        }
                        else{
                            $("#error-msg").css("display", "block");
                            setTimeout(function(){
                                $("#error-msg").css("display", "none")
                            }, 2500);
                        }
                    },
                    error: function(r){
                        $("#error-msg").css("display", "block");
                        setTimeout(function(){
                            $("#error-msg").css("display", "none")
                        }, 2500);
                    }
                });
            });

            <?php
                if($enableThemes){
                    echo '$("#theme").change(function(){
                            var theme = $("#theme")[0][$("#theme")[0].selectedIndex].value;
                            var root = document.documentElement;
            
                            switch (theme) {
                                case "denvelope":
                                    root.style.setProperty("--header-color", "#160C28");
                                    root.style.setProperty("--body-color", '; if($monochromaticBody) echo '"#160C28"'; else echo '"#000411"'; /*only in themes with different header and body color*/ echo');
                                    root.style.setProperty("--text-color", "#EFCB68");
                                    root.style.setProperty("--input-bgcolor", "rgba(22, 12, 40, 0.7)"); //header color to rgb
                                    root.style.setProperty("--form-bg-image-gradient-color", "rgba(0, 4, 17, 0.5)"); //body color to rgb
                                    root.style.setProperty("--form-bg-image-gradient-color-mob", "rgba(22, 12, 40, 0.5)"); //header color to rgb
                                    root.style.setProperty("--form-change-text-color", "#E6AF2E");
                                    break;
                                case "deep-koamaru":
                                    root.style.setProperty("--header-color", "#2F3061");
                                    root.style.setProperty("--body-color", "#2F3061");
                                    root.style.setProperty("--text-color", "#FFE66D");
                                    root.style.setProperty("--input-bgcolor", "rgba(47, 48, 97, 0.7)");
                                    root.style.setProperty("--form-bg-image-gradient-color", "rgba(47, 48, 97, 0.5)");
                                    root.style.setProperty("--form-bg-image-gradient-color-mob", "rgba(47, 48, 97, 0.5)");
                                    root.style.setProperty("--form-change-text-color", "#E6AF2E");
                                    break;
                                case "autumn":
                                    root.style.setProperty("--header-color", "#210203");
                                    root.style.setProperty("--body-color", '; if($monochromaticBody) echo '"#210203"'; else echo '"#C17767"'; echo');
                                    root.style.setProperty("--text-color", "#D3B99F");
                                    root.style.setProperty("--input-bgcolor", "rgba(33, 2, 3, 0.7)");
                                    root.style.setProperty("--form-bg-image-gradient-color", "rgba(193, 119, 103, 0.5)");
                                    root.style.setProperty("--form-bg-image-gradient-color-mob", "rgba(33, 2, 3, 0.5)");
                                    root.style.setProperty("--form-change-text-color", "#2F2504");
                                    break;
                                case "moonstone-blue":
                                    root.style.setProperty("--header-color", "#6CA6C1");
                                    root.style.setProperty("--body-color", "#6CA6C1");
                                    root.style.setProperty("--text-color", "#FFE66D");
                                    root.style.setProperty("--input-bgcolor", "rgba(108, 166, 193, 0.7)");
                                    root.style.setProperty("--form-bg-image-gradient-color", "rgba(108, 166, 193, 0.5)");
                                    root.style.setProperty("--form-bg-image-gradient-color-mob", "rgba(108, 166, 193, 0.5)");
                                    root.style.setProperty("--form-change-text-color", "#2F2504");
                                    break;
                                default:
                                    root.style.setProperty("--header-color", "#160C28");
                                    root.style.setProperty("--body-color", "#000411");
                                    root.style.setProperty("--text-color", "#EFCB68");
                                    root.style.setProperty("--input-bgcolor", "rgba(22, 12, 40, 0.7)");
                                    root.style.setProperty("--form-bg-image-gradient-color", "rgba(0, 4, 17, 0.5)");
                                    root.style.setProperty("--form-bg-image-gradient-color-mob", "rgba(22, 12, 40, 0.5)");
                                    root.style.setProperty("--form-change-text-color", "#E6AF2E");
                                    break;
                            }
            
                            var headerColor = root.style.getPropertyValue("--header-color");
            
                            $("meta[name=msapplication-navbutton-color]").attr("content", headerColor);
                            $("meta[name=theme-color]").attr("content", headerColor);
            
                            $.ajax({
                                type: "POST",
                                url: "../../php/change-theme.php",
                                data: "theme=" + theme,
                                dataType: "JSON",
                                success: function(r){
                                    if(r[0][\'themeUpdated\'] == true){
                                        $("#success-msg").css("display", "block");
                                        setTimeout(function(){
                                            $("#success-msg").css("display", "none")
                                        }, 2500);
                                    }
                                },
                                error: function(r){
                                    $("#error-msg").css("display", "block");
                                    setTimeout(function(){
                                        $("#error-msg").css("display", "none")
                                    }, 2500);
                                }
                            });
                        });
                    ';
                }
            ?>

            <?php
                if($enablePayments){
                    echo 'function planIndex(plan) {
                            var index = 0;
                            switch (plan) {
                                case "Free":
                                    index = 0;
                                    break;
                                case "Personal":
                                    index = 1;
                                    break;
                                case "Personal Plus":
                                    index = 2;
                                    break;
                                case "Professional":
                                    index = 3;
                                    break;
                                case "Professional Plus":
                                    index = 4;
                                    break;
                                case "Enterprise":
                                    index = 5;
                                    break;
                            }
            
                            return index;
                        }
                    ';
                }
            ?>

            $(".support-case").click(function(){
                var caseNumber = $(this).children(".support-case-info").children("h4").html().substr(1);
                //var url = "./?case=" + caseNumber;

                //window.location.href = url;
                //history.pushState(null, null, url);

                $("#support-settings-main").css("display", "none");
                $("#support-settings-chat-history").css("display", "block");
                $("#support-cases-settings-back-button").css("display", "block");
                $("#support-settings-chat-history .spinner").css("display", "flex");

                if($(window).width() > 1200){
                    $("#support-messages-container").css("max-height", "calc(100vh - " + $("#account-settings").css("margin-top") + " - " + $("#support-message-bar-respond").outerHeight(true) + "px - " + $("#support-cases-settings-back-button").outerHeight(true) + "px - 2vw)");
                }
                else{
                    $("#support-messages-container").css("max-height", "calc(100vh - " + $("#account-settings").css("margin-top") + " - " + $("#support-message-bar-respond").outerHeight(true) + "px - " + $("#support-cases-settings-back-button").outerHeight(true) + "px - " + $("#main-div-account-settings-mob").outerHeight(true) + "px - 2vw)");
                }

                $.ajax({
                    type: "POST",
                    url: "../../php/get-support-messages.php",
                    data: "support-case-id=" + caseNumber,
                    dataType: "JSON",
                    success: function(r){
                        var messages = r['supportMessages'];

                        if(messages){
                            for(let i = 0; i < messages.length; i++){
                                var time =  messages[i]['time'].substr(0, messages[i]['time'].lastIndexOf(" "));
                                
                                if(messages[i]['username'] != "support"){
                                    $("#support-messages-container").append(
                                        '<div class="user">'
                                        +
                                        '<div class="support-message">'
                                        +
                                        '<p>' + messages[i]['body'] + '</p>'
                                        +
                                        '<p class="sender-info">' + messages[i]['username'] + " " + time + '</p>'
                                        +
                                        '</div>'
                                        +
                                        '</div>'
                                    );
                                }
                                else{
                                    $("#support-messages-container").append(
                                        '<div class="support">'
                                        +
                                        '<div class="support-message">'
                                        +
                                        '<p>' + messages[i]['body'] + '</p>'
                                        +
                                        '<p class="sender-info">' + messages[i]['username'] + " " + time + '</p>'
                                        +
                                        '</div>'
                                        +
                                        '</div>'
                                    );
                                }
                            }

                            $("#support-settings-chat-history .spinner").css("display", "none");
                            $("#support-message-bar-respond").css("display", "block");
                            $("#case-number").val(caseNumber);
                        }
                        else{

                        }
                    },
                    error: function(r){
                        
                    }
                });
            });

            $("#support-cases-settings-back-button").click(function(){
                $("#support-settings-chat-history").css("display", "none");
                $("#support-cases-settings-back-button").css("display", "none");
                $("#support-settings-chat-history .spinner").css("display", "none");
                $("#support-messages-container").html("");
                $("input#message").val("");
                $("p.char-counter-support-message span").html("0");
                $(".error-message").css("display", "none");
                $("#support-settings-main").css("display", "block");
            });

            $("input#message").on("keydown input", function(){
                $("p.char-counter-support-message span").html($("input#message").val().length);

                if($("input#message").val().length > 5000){
                    $("#error-message-reply-too-long").css("display", "block");
                }
                else if($("input#message").val().length == 0){
                    $("#error-message-reply-empty").css("display", "block");
                }
                else{
                    $(".error-message").css("display", "none");
                }
            });

            $("#send-reply-button").click(function(e){
                if($("#message").val().length > 5000){
                    $("#error-message-reply-too-long").css("display", "block");
                    e.preventDefault();
                }
                else if($("#message").val().length == 0){
                    $("#error-message-reply-empty").css("display", "block");
                    e.preventDefault();
                }
                else{
                    $(".error-message").css("display", "none");
                }
            });
        });

        $("#main-div-account-settings-mob").css("margin-top", $("#header").outerHeight(true));
        $("#main-div-account-settings-mob").css("top", $("#header").outerHeight(true));
        $("#account-settings-div").css("margin-top", $("#header").outerHeight(true) + ($("#header").outerHeight(true) / 2));
        $("#account-settings").css("margin-top", $("#header").outerHeight(true) + ($("#header").outerHeight(true) / 2));

        $(window).on("load", function(){
            $("#main-div-account-settings-mob").css("margin-top", $("#header").outerHeight(true));
            $("#main-div-account-settings-mob").css("top", $("#header").outerHeight(true));
            $("#account-settings-div").css("margin-top", $("#header").outerHeight(true) + ($("#header").outerHeight(true) / 2));
            $("#account-settings").css("margin-top", $("#header").outerHeight(true) + ($("#header").outerHeight(true) / 2));
        });

        $(window).resize(function(){
            if($("#menu-mob").css("display") == "none"){
                $("#main-div-account-settings-mob").css("margin-top", $("#header").outerHeight(true));
                $("#main-div-account-settings-mob").css("top", $("#header").outerHeight(true));
                $("#account-settings-div").css("margin-top", $("#header").outerHeight(true) + ($("#header").outerHeight(true) / 2));
                $("#account-settings").css("margin-top", $("#header").outerHeight(true) + ($("#header").outerHeight(true) / 2));
            }

            if($(window).width() > 1200){
                $("#support-messages-container").css("max-height", "calc(100vh - " + $("#account-settings").css("margin-top") + " - " + $("#support-message-bar-respond").outerHeight(true) + "px - " + $("#support-cases-settings-back-button").outerHeight(true) + "px - 2vw)");
            }
            else{
                $("#support-messages-container").css("max-height", "calc(100vh - " + $("#account-settings").css("margin-top") + " - " + $("#support-message-bar-respond").outerHeight(true) + "px - " + $("#support-cases-settings-back-button").outerHeight(true) + "px - " + $("#main-div-account-settings-mob").outerHeight(true) + "px - 2vw)");
            }
        });

        $(window).on("hashchange", function(){
            var url = window.location.href;
            var anchor = url.split("#")[1];
            
            switch (anchor) {
                case "general":
                    general(true);
                    break;
                case "security":
                    security(true);
                    break;
                case "advanced":
                    advanced(true);
                    break;
                case "info":
                    info(true);
                    break;
                <?php
                    if($enablePayments){
                        echo 'case "plan":
                                plan(true);
                                break;
                        ';
                    }
                ?>
                case "privacy":
                    privacy(true);
                    break;
                case "support":
                    support(true);
                    break;
                default:
                    break;
            }
        });

        $(document).on("contextmenu", function(){
            return false;
        });

        $(document).click(function(e){
            if(!$(e.target).is("#delete-account-button")){
                firstButtonClickDeleteAccount = true;
                $("#delete-account-p").css("display", "none");
            }
            
            if(!$(e.target).is("#cancel-plan-button")){
                firstButtonClickCancelPlan = true;
                $("#disclaimer-plan-choice").css("display", "none");
                $(".cancel-plan-button-disclaimer-elements").remove();
            }
        });

        <?php
            if($enablePayments){
                echo "// Create a Stripe client.
                    var stripe = Stripe('"; echo $stripePublicAPIKey; echo"');
            
                    // Create an instance of Elements.
                    var elements = stripe.elements();
            
                    // Custom styling can be passed to options when creating an Element.
                    // (Note that this demo uses a wider set of styles than the guide below.)
                    var style = {
                        base: {
                            color: '#000411',
                            fontFamily: '"; echo'"Ubuntu Mono"'; echo", monospace',
                            fontSmoothing: 'antialiased',
                            fontSize: '16px',
                            fontWeight: '700',
                            '::placeholder': {
                                color: '#000411'
                            }
                        },
                        invalid: {
                            color: '#fa755a',
                            iconColor: '#fa755a'
                        }
                    };
            
                    // Create an instance of the card Element.
                    var card = elements.create('card', {style: style});
            
                    // Add an instance of the card Element into the `card-element` <div>.
                    card.mount('#card-element');
            
                    // Handle real-time validation errors from the card Element.
                    card.addEventListener('change', function(event) {
                    var displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                    });
            
                    // Handle form submission.
                    var form = document.getElementById('payment-form');
                    form.addEventListener('submit', function(event) {
                    event.preventDefault();
            
                    stripe.createToken(card).then(function(result) {
                        if (result.error) {
                        // Inform the user if there was an error.
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                        } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                        }
                    });
                    });
            
                    // Submit the form with the token ID.
                    function stripeTokenHandler(token) {
                    // Insert the token ID into the form so it gets submitted to the server
                    var form = document.getElementById('payment-form');
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', token.id);
                    form.appendChild(hiddenInput);
            
                    // Submit the form
                    form.submit();
                    }
                ";
            }
        ?>
    </script>

</body>
</html>