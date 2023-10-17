<?php
    if(isset($_POST['delete-account-button'])){
        session_start();

        require("dbh.php");
        require("remove-dir.php");
        require("add-log.php");
        require("delete-all-user-data.php");

        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['sqlError'] = true;

            header("Location: ../");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            removeDir("../u/".$user['userID']);

            deleteAllUserData();

            mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
            mysqli_stmt_execute($stmt);

            addLog("ACCOUNT_DELETE");

            header("Location: ../");
            exit();
        }
    }
    else{
        header("Location: ../account/settings");
        exit();
    }
?>