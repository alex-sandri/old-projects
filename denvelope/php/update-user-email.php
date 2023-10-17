<?php
    function updateUserEmail($newEmail){
        require("dbh.php");
        require("global-vars.php");
        require("../vendor/autoload.php");
        require("get-customer.php");
        require("db-info.php");
        require("has-2fa.php");
        require("../authy-functions/update-authy-user-email.php");

        $numOfTables = count($tables);

        $stmt = mysqli_stmt_init($conn);

        mysqli_autocommit($conn, FALSE);

        for ($i = 0; $i < $numOfTables; $i++) {
            $sqlQuery = "UPDATE ".$tables[$i]." SET ".$emailFields[$i]."=? WHERE ".$usernameFields[$i]."=? OR ".$emailFields[$i]."=?";

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                echo 'An error occurred while processing the request';
                exit();
            }

            mysqli_stmt_bind_param($stmt, "sss", $newEmail, $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);
        }

        mysqli_commit($conn);
        mysqli_autocommit($conn, TRUE);

        \Stripe\Stripe::setApiKey($stripeSecretAPIKey);

        $customer = getCustomer();

        if($customer){
            \Stripe\Customer::update(
                $customer['customerID'],
                [
                    'email' => $newEmail
                ]
            );
        }

        if(has2FA()){
            updateAuthyUserEmail($newEmail);
        }
    }
?>