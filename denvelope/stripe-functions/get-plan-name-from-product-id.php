<?php
    function getPlanNameFromProductID($productID){
        require_once("../vendor/autoload.php");
        require("../php/global-vars.php");

        \Stripe\Stripe::setApiKey($stripeSecretAPIKey);

        $plan = \Stripe\Product::retrieve($productID);
        
        return $plan['name'];
    }
?>