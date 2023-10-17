<?php
    function getCustomer(){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM customers WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $customer = mysqli_fetch_assoc($result);

        return $customer;
    }
?>