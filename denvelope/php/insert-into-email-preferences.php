<?php
    function insertIntoEmailPreferences($username, $email){
        require("dbh.php");

        $sqlQuery = "INSERT INTO email_preferences (username, email) VALUES (?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo "An error occurred while processing the request";
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
    }
?>