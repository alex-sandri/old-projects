<?php
    function getUserFromCustomerID($customerID){
        require("../php/dbh.php");
        
        $sqlQuery = "SELECT * FROM customers WHERE customerID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            http_response_code(500);
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $customerID);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $customer = mysqli_fetch_assoc($result);
        
        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            http_response_code(500);
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $customer['username'], $customer['email']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        return $user;
    }
?>