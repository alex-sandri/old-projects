<?php
    function sendEmailPasswordChanged($to){
        require("send-email-ses.php");
        require_once("get-user-preferred-language.php");

        $lang = getUserPreferredLanguageByEmail($to);
        require("../lang/".$lang.".php");

        $subject = getTranslatedContent("email_password_changed_subject");

        $plaintext_body = getTranslatedContent("email_password_changed_password_correctly_reset")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_password_changed_did_not_perform_action_enter_email")." ($to) ".getTranslatedContent("email_password_changed_did_not_perform_action_enter_email_into_form").":\r\n";
        $plaintext_body .= "https://denvelope.com/forgot/\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_password_changed_do_not_share_your_password")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_all_the_best")."\r\n";
        $plaintext_body .= getTranslatedContent("email_the_denvelope_team");

        $html_body = "";

        sendEmailSES($to, $subject, $plaintext_body, $html_body);
    }
?>