<?php
    function sendEmailAccountConfirmed($to){
        require("send-email-ses.php");
        require_once("get-user-preferred-language.php");

        $lang = getUserPreferredLanguageByEmail($to);
        require("../lang/".$lang.".php");

        $subject = getTranslatedContent("email_account_confirmed_subject");

        $plaintext_body = getTranslatedContent("email_account_confirmed_congratulations")."\r\n";
        $plaintext_body .= getTranslatedContent("email_account_confirmed_account_confirmed")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_account_confirmed_enjoy_what_we_offer")."\r\n";
        $plaintext_body .= getTranslatedContent("email_account_confirmed_in_case_need_help").":\r\n\r\n";
        $plaintext_body .= "support@denvelope.com\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_account_confirmed_help_through_contact_form").":\r\n";
        $plaintext_body .= "https://denvelope.com/contact\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_all_the_best")."\r\n";
        $plaintext_body .= getTranslatedContent("email_the_denvelope_team");

        $html_body = "";

        sendEmailSES($to, $subject, $plaintext_body, $html_body);
    }
?>