<?php
    function sendEmailSubscriptionUpdated($to, $oldPlan, $newPlan, $accountEmptied){
        require("../php/send-email-ses.php");
        require_once("../php/get-user-preferred-language.php");

        $lang = getUserPreferredLanguageByEmail($to);
        require("../lang/".$lang.".php");

        $subject = getTranslatedContent("email_subscription_updated_subject");

        if($oldPlan == "Free"){
            $plaintext_body = getTranslatedContent("email_subscription_updated_from_free_to")." $newPlan".getTranslatedContent("email_subscription_updated_from_free_to_tier")."\r\n\r\n";
        }
        else{
            $plaintext_body = getTranslatedContent("email_subscription_updated_from")." $oldPlan ".getTranslatedContent("email_subscription_updated_from_to")." $newPlan".getTranslatedContent("email_subscription_updated_from_to_tier")."\r\n\r\n";
        }

        if($accountEmptied){ //only on downgrade
            $plaintext_body .= getTranslatedContent("email_subscription_updated_total_size_greater_new")." ".$newPlan." ".getTranslatedContent("email_subscription_updated_total_size_greater_new_tier")."\r\n";
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