<?php
    function handleEndedSubscription($subscription){
        require_once("../vendor/autoload.php");
        require("../php/global-vars.php");
        require_once("delete-customer-from-db.php");
        require_once("downgrade-user-to-free-plan.php");
        require_once("get-user-from-customer-id.php");
        require_once("../php/convert-to-bytes.php");
        require_once("../php/remove-dir.php");
        require_once("send-email-ended-subscription.php");
        require_once("get-plan-name-from-product-id.php");

        \Stripe\Stripe::setApiKey($stripeSecretAPIKey);

        $customer = \Stripe\Customer::retrieve($subscription['customer']);
        $customer->delete();

        $user = getUserFromCustomerID($subscription['customer']);

        downgradeUserToFreePlan($user['userID']);

        deleteCustomerFromDB($subscription['customer']);

        $accountEmptied = false;

        if($user['usedStorageInBytes'] > convertToBytes($FREE_TIER_STORAGE)){
            removeDir("../u/".$user['userID']);

            $accountEmptied = true;
        }

        $planName = getPlanNameFromProductID($subscription['items']['data'][0]['plan']['product']);

        sendEmailEndedSubscription($user['email'], $planName, $accountEmptied);
    }
?>