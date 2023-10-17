<?php
    function handleSubscriptionUpdated($subscription){
        require("../php/global-vars.php");
        require_once("get-user-from-customer-id.php");
        require_once("update-customer-subscription.php");
        require_once("../php/convert-to-bytes.php");
        require_once("../php/remove-dir.php");
        require_once("send-email-subscription-updated.php");
        require_once("get-plan-name-from-product-id.php");

        $user = getUserFromCustomerID($subscription['customer']);

        updateCustomerSubscription($subscription, $user);

        $accountEmptied = false;

        if($user['usedStorageInBytes'] > convertToBytes($FREE_TIER_STORAGE)){
            removeDir("../u/".$user['userID']);

            $accountEmptied = true;
        }

        $oldPlanName = getPlanNameFromProductID($subscription['previous_attributes']['items']['data'][0]['plan']['product']);
        $newPlanName = getPlanNameFromProductID($subscription['items']['data'][0]['plan']['product']);

        sendEmailSubscriptionUpdated($user['email'], $oldPlanName, $newPlanName, $accountEmptied);
    }
?>