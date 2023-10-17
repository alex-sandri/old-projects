<?php
    function sendEmailNewLogin($to, $os, $browser, $ip, $time, $sessionLogoutID, $location){
        require("send-email-ses.php");
        require("get-email-preferences.php");
        require_once("get-user-preferred-language.php");

        $lang = getUserPreferredLanguageByEmail($to);
        require("../lang/".$lang.".php");

        $preferences = getEmailPreferences();

        if($preferences['onNewLogins']){
            $subject = getTranslatedContent("email_new_login_subject");

            $plaintext_body = getTranslatedContent("email_new_login_login_notice")."\r\n\r\n";
            $plaintext_body .= getTranslatedContent("email_new_login_informations")."*:\r\n\r\n";
            $plaintext_body .= getTranslatedContent("email_new_login_platform").": ".$os."\r\n";
            $plaintext_body .= getTranslatedContent("email_new_login_browser").": ".$browser."\r\n";
            $plaintext_body .= getTranslatedContent("email_new_login_ip_address").": ".$ip."\r\n";
            $plaintext_body .= getTranslatedContent("email_new_login_time").": ".$time."\r\n\r\n";
            $plaintext_body .= getTranslatedContent("email_new_login_location").": ".$location."\r\n\r\n";
            $plaintext_body .= "*".getTranslatedContent("email_new_login_note_information_accuracy")."\r\n\r\n";
            $plaintext_body .= getTranslatedContent("email_new_login_invalidate_session")."\r\n";
            $plaintext_body .= "https://denvelope.com/logout/?session_id=$sessionLogoutID\r\n\r\n";
            $plaintext_body .= getTranslatedContent("email_new_login_security_concerns_change_password").":\r\n";
            $plaintext_body .= "https://denvelope.com/account/settings\r\n\r\n";
            $plaintext_body .= getTranslatedContent("email_all_the_best")."\r\n";
            $plaintext_body .= getTranslatedContent("email_the_denvelope_team");

            $html_body = "";

            sendEmailSES($to, $subject, $plaintext_body, $html_body);
        }
    }
?>