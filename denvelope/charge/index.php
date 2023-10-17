<?php
    if(isset($_POST['selected-plan'])){
        session_start();

        require("../vendor/autoload.php");
        require("../php/dbh.php");
        require("../php/global-vars.php");
        require("../php/std-date.php");
        require("../php/get-customer.php");
        require("../php/add-log.php");
        require_once("../php/get-user-preferred-language.php");

        \Stripe\Stripe::setApiKey($stripeSecretAPIKey);

        $plan = $_POST['selected-plan'];
        $token = isset($_POST['stripeToken']) ? $_POST['stripeToken'] : "not-needed"; //not-needed is used when changing plan
        
        if($plan == $_SESSION['plan']){
            $_SESSION['changePlanError'] = "samePlan";

            header("Location: ../account/settings/#plan");
            exit();
        }

        if($plan != "Free" && $plan != "Personal" && $plan != "Personal Plus" && $plan != "Professional" && $plan != "Professional Plus" && $plan != "Enterprise"){
            header("Location: ../account/settings/#plan");
            exit();
        }

        if($plan == "Free"){
            $maxStorage = $FREE_TIER_STORAGE;
        }
        else if($plan == "Personal"){
            $planID = $PERSONAL_TIER_STRIPE_PLAN_ID;
            $maxStorage = $PERSONAL_TIER_STORAGE;
        }
        else if($plan == "Personal Plus"){
            $planID = $PERSONAL_PLUS_TIER_STRIPE_PLAN_ID;
            $maxStorage = $PERSONAL_PLUS_TIER_STORAGE;
        }
        else if($plan == "Professional"){
            $planID = $PROFESSIONAL_TIER_STRIPE_PLAN_ID;
            $maxStorage = $PROFESSIONAL_TIER_STORAGE;
        }
        else if($plan == "Professional Plus"){
            $planID = $PROFESSIONAL_PLUS_TIER_STRIPE_PLAN_ID;
            $maxStorage = $PROFESSIONAL_PLUS_PLUSPLUS_TIER_STORAGE;
        }
        else{
            $planID = $ENTERPRISE_TIER_STRIPE_PLAN_ID;
            $maxStorage = $ENTERPRISE_TIER_STORAGE;
        }

        if(planIndex($plan) > planIndex($_SESSION['plan'])){
            if($_SESSION['plan'] == "Free"){
                $customer = \Stripe\Customer::create(array(
                    "email" => $_SESSION['email'],
                    "name" => $_SESSION['username'],
                    "preferred_locales" => [
                        getUserPreferredLanguageForStripeLocale()
                    ],
                    "source" => $token
                ));
        
                $subscription = \Stripe\Subscription::create(array(
                    "customer" => $customer['id'],
                    "items" => [
                        [
                            "plan" => $planID
                        ]
                    ]
                ));

                $sqlQuery = "INSERT INTO customers (username, email, customerID, subscriptionID, plan, cardIcon, cardType, lastFourDigits, expirationDate, created, nextRenewal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $_SESSION['changePlanError'] = "sqlError";

                    header("Location: ../account/settings/#plan");
                    exit();
                }

                $token = \Stripe\Token::retrieve($token);

                if(strpos(strtolower($token['card']['brand']), "visa") > -1){
                    $cardIcon = "<i class='fab fa-cc-visa'></i>";
                }
                else if(strpos(strtolower($token['card']['brand']), "mastercard") > -1){
                    $cardIcon = "<i class='fab fa-cc-mastercard'></i>";
                }
                else if(strpos(strtolower($token['card']['brand']), "american express") > -1){
                    $cardIcon = "<i class='fab fa-cc-amex'></i>";
                }
                else if(strpos(strtolower($token['card']['brand']), "discover") > -1){
                    $cardIcon = "<i class='fab fa-cc-discover'></i>";
                }
                else if(strpos(strtolower($token['card']['brand']), "diners club") > -1){
                    $cardIcon = "<i class='fab fa-cc-diners-club'></i>";
                }
                else if(strpos(strtolower($token['card']['brand']), "jcb") > -1){
                    $cardIcon = "<i class='fab fa-cc-jcb'></i>";
                }
                else{
                    $cardIcon = '<i class="fas fa-credit-card"></i>';
                }

                $expirationDate = $token['card']['exp_month']." / ".$token['card']['exp_year'];
                $created = stdDateFromUnixTime($subscription['created']);
                $nextRenewal = date("j/m/Y", $subscription['current_period_end']);

                mysqli_stmt_bind_param($stmt, "sssssssssss", $_SESSION['username'], $_SESSION['email'], $customer['id'], $subscription['id'], $plan, $cardIcon, $token['card']['brand'], $token['card']['last4'], $expirationDate, $created, $nextRenewal);
                mysqli_stmt_execute($stmt);

                $sqlQuery = "UPDATE users SET plan=?, maxStorage=? WHERE username=? OR email=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $_SESSION['changePlanError'] = "sqlError";

                    header("Location: ../account/settings/#plan");
                    exit();
                }

                mysqli_stmt_bind_param($stmt, "ssss", $plan, $maxStorage, $_SESSION['username'], $_SESSION['email']);
                mysqli_stmt_execute($stmt);

                addLog("CUSTOMER_CREATED");
            }
            else{
                $customer = getCustomer();

                $subscription = \Stripe\Subscription::retrieve($customer['subscriptionID']);

                $subscription = \Stripe\Subscription::update(
                    $customer['subscriptionID'],
                    [
                        "billing_cycle_anchor" => "now",
                        "items" => [
                            [
                                "id" => $subscription->items->data[0]->id,
                                "plan" => $planID
                            ]
                        ]
                    ]
                );

                $sqlQuery = "UPDATE customers SET plan=?, nextRenewal=? WHERE username=? OR email=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $_SESSION['changePlanError'] = "sqlError";

                    header("Location: ../account/settings/#plan");
                    exit();
                }

                $nextRenewal = date("j/m/Y", $subscription['current_period_end']);

                mysqli_stmt_bind_param($stmt, "ssss", $plan, $nextRenewal, $_SESSION['username'], $_SESSION['email']);
                mysqli_stmt_execute($stmt);

                $sqlQuery = "UPDATE users SET plan=?, maxStorage=? WHERE username=? OR email=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $_SESSION['changePlanError'] = "sqlError";

                    header("Location: ../account/settings/#plan");
                    exit();
                }

                mysqli_stmt_bind_param($stmt, "ssss", $plan, $maxStorage, $_SESSION['username'], $_SESSION['email']);
                mysqli_stmt_execute($stmt);

                addLog("SUBSCRIPTION_UPGRADED");
            }
        }
        else{
            if($plan == "Free"){
                $customer = getCustomer();

                $sqlQuery = "UPDATE customers SET nextRenewal=? WHERE username=? OR email=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $_SESSION['cancelPlanError'] = "sqlError";

                    header("Location: ../account/settings/#plan");
                    exit();
                }

                $nextRenewal = "cancelled||".$customer['nextRenewal'];

                mysqli_stmt_bind_param($stmt, "sss", $nextRenewal, $_SESSION['username'], $_SESSION['email']);
                mysqli_stmt_execute($stmt);

                \Stripe\Subscription::update(
                    $customer['subscriptionID'],
                    [
                        'cancel_at_period_end' => true
                    ]
                );
                
                addLog("SUBSCRIPTION_CANCELLED_BY_DOWNGRADE");
            }
            else{
                $customer = getCustomer();

                $subscription = \Stripe\Subscription::retrieve($customer['subscriptionID']);

                $subscription = \Stripe\Subscription::update(
                    $customer['subscriptionID'],
                    [
                        "items" => [
                            [
                                "id" => $subscription->items->data[0]->id,
                                "plan" => $planID
                            ]
                        ]
                    ]
                );

                $sqlQuery = "UPDATE customers SET nextRenewal=? WHERE username=? OR email=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $_SESSION['cancelPlanError'] = "sqlError";

                    header("Location: ../account/settings/#plan");
                    exit();
                }

                $nextRenewal = "downgraded||".$plan."||".date("j/m/Y", $subscription['current_period_end']);

                mysqli_stmt_bind_param($stmt, "sss", $nextRenewal, $_SESSION['username'], $_SESSION['email']);
                mysqli_stmt_execute($stmt);

                addLog("SUBSCRIPTION_DOWNGRADED");
            }
        }
        
        header("Location: ../account/settings/#plan");
        exit();
    }
    else if(isset($_POST['change-cc-button-confirm'])){
        session_start();

        require("../vendor/autoload.php");
        require("../php/dbh.php");
        require("../php/get-customer.php");
        require("../php/add-log.php");

        \Stripe\Stripe::setApiKey($stripeSecretAPIKey);

        $token = $_POST['stripeToken'];

        $customer = getCustomer();

        $customer = \Stripe\Customer::update(
            $customer['customerID'],
            [
                "source" => $token
            ]
        );

        $sqlQuery = "UPDATE customers SET cardIcon=?, cardType=?, lastFourDigits=?, expirationDate=? WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['changePlanError'] = "sqlError";

            header("Location: ../account/settings/#plan");
            exit();
        }

        $token = \Stripe\Token::retrieve($token);

        if(strpos(strtolower($token['card']['brand']), "visa") > -1){
            $cardIcon = "<i class='fab fa-cc-visa'></i>";
        }
        else if(strpos(strtolower($token['card']['brand']), "mastercard") > -1){
            $cardIcon = "<i class='fab fa-cc-mastercard'></i>";
        }
        else if(strpos(strtolower($token['card']['brand']), "american express") > -1){
            $cardIcon = "<i class='fab fa-cc-amex'></i>";
        }
        else if(strpos(strtolower($token['card']['brand']), "discover") > -1){
            $cardIcon = "<i class='fab fa-cc-discover'></i>";
        }
        else if(strpos(strtolower($token['card']['brand']), "diners club") > -1){
            $cardIcon = "<i class='fab fa-cc-diners-club'></i>";
        }
        else if(strpos(strtolower($token['card']['brand']), "jcb") > -1){
            $cardIcon = "<i class='fab fa-cc-jcb'></i>";
        }
        else{
            $cardIcon = '<i class="fas fa-credit-card"></i>';
        }

        $expirationDate = $token['card']['exp_month']." / ".$token['card']['exp_year'];

        mysqli_stmt_bind_param($stmt, "ssssss", $cardIcon, $token['card']['brand'], $token['card']['last4'], $expirationDate, $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        addLog("CREDIT_CARD_CHANGE");

        header("Location: ../account/settings/#plan");
        exit();
    }
    else if(isset($_POST['cancel-plan-button'])){
        session_start();

        require("../vendor/autoload.php");
        require("../php/dbh.php");
        require("../php/global-vars.php");
        require("../php/get-customer.php");
        require("../php/add-log.php");

        \Stripe\Stripe::setApiKey($stripeSecretAPIKey);

        $customer = getCustomer();

        $sqlQuery = "UPDATE customers SET nextRenewal=? WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['cancelPlanError'] = "sqlError";

            header("Location: ../account/settings/#plan");
            exit();
        }

        $nextRenewal = "cancelled||".$customer['nextRenewal'];

        mysqli_stmt_bind_param($stmt, "sss", $nextRenewal, $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        \Stripe\Subscription::update(
            $customer['subscriptionID'],
            [
                'cancel_at_period_end' => true
            ]
        );

        addLog("PLAN_CANCELLED");

        header("Location: ../account/settings/#plan");
        exit();
    }
    else{
        header("Location: ../account/settings/#plan");
        exit();
    }

    function planIndex($plan){

        $index = 0;

        switch ($plan) {
            case 'Free':
                $index = 0;
                break;
            case 'Personal':
                $index = 1;
                break;
            case 'Personal Plus':
                $index = 2;
                break;
            case 'Professional':
                $index = 3;
                break;
            case 'Professional Plus':
                $index = 4;
                break;
            case 'Enterprise':
                $index = 5;
                break;
        }

        return $index;
    }
?>