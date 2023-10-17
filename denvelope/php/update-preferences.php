<?php
    if(isset($_POST['save-email-preferences-button'])){
        session_start();

        require("dbh.php");

        if(isset($_POST['on-new-logins']) && $_POST['on-new-logins'] == "true"){
            $onNewLogins = 1;
        }
        else{
            $onNewLogins = 0;
        }

        $sqlQuery = "UPDATE email_preferences SET onNewLogins=? WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo "An error occurred while processing the request";
            exit();
        }

        mysqli_stmt_bind_param($stmt, "iss", $onNewLogins, $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        header("Location: ../account/settings/");
        exit();
    }
    else{
        header("Location: ../");
        exit();
    }
?>