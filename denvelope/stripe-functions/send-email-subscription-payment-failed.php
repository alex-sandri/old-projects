<?php
    function sendEmailSubscriptionPaymentFailed($to, $plan, $accountEmptied){
        require("../php/send-email-ses.php");
        require_once("../php/get-user-preferred-language.php");

        $lang = getUserPreferredLanguageByEmail($to);
        require("../lang/".$lang.".php");

        $subject = getTranslatedContent("email_subscription_payment_failed_subject");

        $plaintext_body = getTranslatedContent("email_subscription_payment_failed_payment_for")." ".$plan." ".getTranslatedContent("email_subscription_payment_failed_payment_for_plan_failed")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_subscription_payment_failed_we_cancelled_your_subscription")."\r\n";
        $plaintext_body .= getTranslatedContent("email_ended_subscription_account_downgraded_to_free_tier")."\r\n\r\n";

        if($accountEmptied){
            $plaintext_body = getTranslatedContent("email_ended_subscription_total_size_greater_free_tier")."\r\n";
            $plaintext_body .= getTranslatedContent("email_ended_subscription_account_emptied_as_stated_terms_of_service").":\r\n";
            $plaintext_body .= "https://denvelope.com/terms/\r\n\r\n";
            $plaintext_body .= getTranslatedContent("email_ended_subscription_account_emptied_message_before_confirm")."\r\n\r\n";
        }

        $plaintext_body .= getTranslatedContent("email_all_the_best")."\r\n";
        $plaintext_body .= getTranslatedContent("email_the_denvelope_team");

        $html_body = "";

        sendEmailSES($to, $subject, $plaintext_body, $html_body);
    }
?>