<?php
    if(!isset($_POST['device-logout-id']) && !isset($_POST['logout-from-all-button']) && !isset($_GET['session_id'])){
        require("../php/logout.php");
    }
    else if(isset($_POST['device-logout-id']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')){
        session_start();

        require("../php/dbh.php");
        require("../php/add-log.php");

        $sqlQuery = "DELETE FROM sessions WHERE sessionLogoutID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['sqlError'] = "sqlError";

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $_POST['device-logout-id']);
            mysqli_stmt_execute($stmt);

            $logType = "LOGOUT_ACCOUNT_SECURITY";

            addLog($logType);
        }

        $_SESSION['activeDeviceLogoutSuccess'] = true;

        header('Content-type: application/json');

        $JSONdata = array($_SESSION);
        echo json_encode($JSONdata);

        exit();
    }
    else if(isset($_GET['session_id'])){
        session_start();

        require("../php/dbh.php");
        require("../php/add-log.php");

        $destroySession = false;

        if(!isset($_SESSION['username'])){
            $sqlQuery = "SELECT * FROM sessions WHERE sessionLogoutID=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['sqlError'] = "sqlError";

                header("Location: ../account/settings/#security");
                exit();
            }
            else{
                mysqli_stmt_bind_param($stmt, "s", $_GET['session_id']);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                $session = mysqli_fetch_assoc($result);
            }

            $_SESSION['username'] = $session['username'];
            $_SESSION['email'] = $session['email'];

            $destroySession = true;
        }

        $sqlQuery = "DELETE FROM sessions WHERE sessionLogoutID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $_SESSION['sqlError'] = "sqlError";

            header("Location: ../account/settings/#security");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $_GET['session_id']);
            mysqli_stmt_execute($stmt);

            $logType = "LOGOUT_ACCOUNT_SECURITY_NEW_LOGIN_EMAIL";

            addLog($logType);
        }

        if($destroySession){
            session_unset();
            session_destroy();
        }

        header("Location: ../");
        exit();
    }
    else{
        require("../php/logout-from-all-devices.php");

        logoutFromAllDevices("account-security");

        header("Location: ../");
        exit();
    }
?>