<?php
    function sendEmailEmailChanged($oldEmail, $newEmail){
        require("send-email-ses.php");
        require_once("get-user-preferred-language.php");

        $lang = getUserPreferredLanguageByEmail($to);
        require("../lang/".$lang.".php");

        $subject = getTranslatedContent("email_email_changed_subject");

        $plaintext_body = getTranslatedContent("email_email_changed_old_email_request_to")." $newEmail\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_email_changed_old_did_not_perform_action").":\r\n";
        $plaintext_body .= "support@denvelope.com\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_email_changed_old_did_not_perform_action_contact_form_website").":\r\n";
        $plaintext_body .= "https://denvelope.com/contact\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_email_changed_old_remember_not_share_password")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_all_the_best")."\r\n";
        $plaintext_body .= getTranslatedContent("email_the_denvelope_team");

        $html_body = "";

        sendEmailSES($oldEmail, $subject, $plaintext_body, $html_body);

        $plaintext_body = getTranslatedContent("email_email_changed_new_email_updated_correctly")." $oldEmail\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_email_changed_new_did_not_perform_action").":\r\n";
        $plaintext_body .= "support@denvelope.com\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_email_changed_new_did_not_perform_action_contact_form_website").":\r\n";
        $plaintext_body .= "https://denvelope.com/contact\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_email_changed_new_did_not_perform_action_subject")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_email_changed_new_alternative_contact")." $oldEmail ".getTranslatedContent("email_email_changed_new_alternative_contact_wrong_email_typed")."\r\n";
        $plaintext_body .= getTranslatedContent("email_email_changed_new_contact_support_for_reset")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_all_the_best")."\r\n";
        $plaintext_body .= getTranslatedContent("email_the_denvelope_team");

        $html_body = "";

        sendEmailSES($newEmail, $subject, $plaintext_body, $html_body);
    }
?>