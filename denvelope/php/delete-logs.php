<?php
    if(isset($_POST['delete-logs-button'])){
        
        session_start();

        require("dbh.php");

        if(isset($_POST['opt-out-logs']) && $_POST['opt-out-logs'] == true){
            $optOut = true;

            $sqlQuery = "UPDATE users SET optOutLogCollection=1 WHERE username=? OR email=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                header("Location: ../account/settings/#privacy");
                exit();
            }

            mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);
        }
        else{
            $optOut = false;

            $sqlQuery = "UPDATE users SET optOutLogCollection=0 WHERE username=? OR email=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                header("Location: ../account/settings/#privacy");
                exit();
            }

            mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);
        }

        $sqlQuery = "DELETE FROM logs WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            header("Location: ../account/settings/#privacy");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        header("Location: ../account/settings/#privacy");
        exit();
    }
    else{
        header("Location: ../");
        exit();
    }
?>