<?php
    function updateUserUsername($newUsername){
        require("dbh.php");
        require("global-vars.php");
        require("../vendor/autoload.php");
        require("get-customer.php");
        require("db-info.php");

        $numOfTables = count($tables);

        $stmt = mysqli_stmt_init($conn);

        mysqli_autocommit($conn, FALSE);

        for ($i = 0; $i < $numOfTables; $i++) {
            $sqlQuery = "UPDATE ".$tables[$i]." SET ".$usernameFields[$i]."=? WHERE ".$usernameFields[$i]."=? OR ".$emailFields[$i]."=?";

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                echo 'An error occurred while processing the request';
                exit();
            }

            mysqli_stmt_bind_param($stmt, "sss", $newUsername, $_SESSION['username'], $_SESSION['email']);
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
                    'name' => $newUsername
                ]
            );
        }
    }
?>