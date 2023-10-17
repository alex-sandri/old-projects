<?php
    function deleteCustomerFromDB($customerID){
        require("../php/dbh.php");
        
        $sqlQuery = "DELETE FROM customers WHERE customerID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            http_response_code(500);
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $customerID);
        mysqli_stmt_execute($stmt);
    }
?>