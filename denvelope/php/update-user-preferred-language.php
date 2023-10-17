<?php
    function updatePreferredLanguage($language){
        require("dbh.php");
        require_once("../vendor/autoload.php");
        require("global-vars.php");
        require("get-customer.php");
        require_once("get-user-preferred-language.php");

        $sqlQuery = "UPDATE users SET preferredLanguage=? WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $language, $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        \Stripe\Stripe::setApiKey($stripeSecretAPIKey);

        $customer = getCustomer();

        if($customer){
            \Stripe\Customer::update(
                $customer['customerID'],
                [
                    'preferred_locales' => [
                        getUserPreferredLanguageForStripeLocale(),
                    ]
                ]
            );
        }
    }
?>