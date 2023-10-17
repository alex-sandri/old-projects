<?php
    function sendEmailSupportRequestReceived($to, $caseNumber){
        require_once("send-email-ses.php");
        require_once("get-user-preferred-language.php");

        $lang = getUserPreferredLanguageByEmail($to);
        require("../lang/".$lang.".php");

        $subject = getTranslatedContent("email_support_request_received_subject")." [".getTranslatedContent("email_support_request_received_subject_case")." #$caseNumber]";

        $plaintext_body = getTranslatedContent("email_support_request_received_thank_you_for_contacting")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_support_request_received_new_support_case_opened")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_support_request_received_case_details").":\r\n";
        $plaintext_body .= getTranslatedContent("email_support_request_received_case_id").": ".$caseNumber."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_support_request_received_note_answer_time")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_all_the_best")."\r\n";
        $plaintext_body .= getTranslatedContent("email_the_denvelope_team");

        $html_body = "";

        sendEmailSES($to, $subject, $plaintext_body, $html_body);
    }
?>