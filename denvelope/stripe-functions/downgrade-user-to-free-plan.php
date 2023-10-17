<?php
    function downgradeUserToFreePlan($userID){
        require("../php/dbh.php");
        require("../php/global-vars.php");
        
        $sqlQuery = "UPDATE users SET plan='Free', maxStorage='$FREE_TIER_STORAGE' WHERE userID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            http_response_code(500);
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $userID);
        mysqli_stmt_execute($stmt);
    }
?>