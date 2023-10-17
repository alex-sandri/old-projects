<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        session_start();

        require("dbh.php");

        if(isset($_POST['opt-in']) && $_POST['opt-in'] == "true"){
            $optOut = 0;
        }
        else{
            $optOut = 1;
        }

        $sqlQuery = "UPDATE users SET optOutLogCollection=$optOut WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $_SESSION['optedOut'] = true;

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