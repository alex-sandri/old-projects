<?php
    function handleSubscriptionPaymentFailed($invoice){
        require_once("../vendor/autoload.php");
        require("../php/global-vars.php");
        require_once("delete-customer-from-db.php");
        require_once("downgrade-user-to-free-plan.php");
        require_once("get-user-from-customer-id.php");
        require_once("../php/convert-to-bytes.php");
        require_once("../php/remove-dir.php");
        require_once("send-email-subscription-payment-failed.php");
        require_once("get-plan-name-from-product-id.php");

        \Stripe\Stripe::setApiKey($stripeSecretAPIKey);

        $customer = \Stripe\Customer::retrieve($invoice['customer']);
        $customer->delete();

        downgradeUserToFreePlan($invoice['customer']);

        $user = getUserFromCustomerID($invoice['customer']);

        deleteCustomerFromDB($invoice['customer']);

        $accountEmptied = false;

        if($user['usedStorageInBytes'] > convertToBytes($FREE_TIER_STORAGE)){
            removeDir("../u/".$user['userID']);

            $accountEmptied = true;
        }

        $planName = getPlanNameFromProductID($invoice['lines']['data'][0]['plan']['product']);

        sendEmailSubscriptionPaymentFailed($user['email'], $planName, $accountEmptied);
    }
?>