<?php
    // Disabled
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_POST['send-sms']) || false){
        session_start();

        require("../php/dbh.php");

        $sendSMS = $_POST['send-sms'];

        if(isset($_POST['send-sms']) && $_POST['send-sms'] == "true"){
            $sendSMS = 1;
        }
        else{
            $sendSMS = 0;
        }

        $sqlQuery = "UPDATE authy_users SET sendSMS=? WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "iss", $sendSMS, $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $_SESSION['SMSPreferenceUpdated'] = true;

        header('Content-type: application/json');

        $JSONdata = array($_SESSION);
        echo json_encode($JSONdata);

        exit();
    }
    else{
        header("Location: ../");
        exit();
    }
?>