<?php
    function sendEmailEndedSubscription($to, $plan, $accountEmptied){
        require("../php/send-email-ses.php");
        require_once("../php/get-user-preferred-language.php");

        $lang = getUserPreferredLanguageByEmail($to);
        require("../lang/".$lang.".php");

        $subject = getTranslatedContent("email_ended_subscription_subject");

        $plaintext_body = getTranslatedContent("email_ended_subscription_sorry_see_you_go")."\r\n\r\n";
        $plaintext_body .= getTranslatedContent("email_ended_subscription_your_subscription_to_the")." ".$plan." ".getTranslatedContent("email_ended_subscription_your_subscription_to_the_plan_has_ended")."\r\n";
        $plaintext_body .= getTranslatedContent("email_ended_subscription_account_downgraded_to_free_tier")."\r\n\r\n";

        if($accountEmptied){
            $plaintext_body = getTranslatedContent("email_ended_subscription_total_size_greater_free_tier")."\r\n";
            $plaintext_body .= getTranslatedContent("email_ended_subscription_account_emptied_as_stated_terms_of_service").":\r\n";
            $plaintext_body .= "https://denvelope.com/terms/\r\n\r\n";
            $plaintext_body .= getTranslatedContent("email_ended_subscription_account_emptied_message_before_confirm")."\r\n\r\n";
        }

        $plaintext_body .= getTranslatedContent("email_ended_subscription_always_working_to_make_users_happy")."\r\n";
        $plaintext_body .= getTranslatedContent("email_ended_subscription_if_have_time_consider_leaving_feedback").":\r\n";
        $plaintext_body .= "feedback@denvelope.com\r\n\r\n";

        $plaintext_body .= getTranslatedContent("email_all_the_best")."\r\n";
        $plaintext_body .= getTranslatedContent("email_the_denvelope_team");

        $html_body = "";

        sendEmailSES($to, $subject, $plaintext_body, $html_body);
    }
?>