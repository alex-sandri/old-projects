<?php
    function updateCustomerSubscription($subscription, $user){
        require("../php/dbh.php");
        require_once("get-user-from-customer-id.php");
        require_once("get-plan-name-from-product-id.php");

        $sqlQuery = "UPDATE customers SET plan=?, nextRenewal=? WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            http_response_code(500);
            exit();
        }

        $planName = getPlanNameFromProductID($subscription['items']['data'][0]['plan']['product']);
        $nextRenewal = date("j/m/Y", $subscription['current_period_end']);

        mysqli_stmt_bind_param($stmt, "ssss", $planName, $nextRenewal, $user['username'], $user['email']);
        mysqli_stmt_execute($stmt);
    }
?>