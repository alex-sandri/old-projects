<?php
    function logoutFromAllDevices($from){
        session_start();

        require("dbh.php");
        require("add-log.php");

        $sqlQuery = "DELETE FROM sessions WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['sqlError'] = "sqlError";

            if($from == "account-security"){
                header("Location: ../account/settings/#security");
            }
            else{
                header("Location: ../");
            }

            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);

            $logType = "LOGOUT_FROM_ALL";

            addLog($logType);
        }
    }
?>