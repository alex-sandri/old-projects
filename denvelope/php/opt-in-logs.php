<?php
    if(isset($_POST['opt-in-logs-button'])){
        session_start();

        require("dbh.php");

        $sqlQuery = "UPDATE users SET optOutLogCollection=0 WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

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