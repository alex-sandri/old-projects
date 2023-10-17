<?php
    session_start();

    require("dbh.php");
    require("add-log.php");
    require("global-vars.php");

    if(isset($_COOKIE['userSession'])){

        $sqlQuery = "DELETE FROM sessions WHERE sessionID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $sqlError = "sqlError";

            header("Location: ../");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $_COOKIE['userSession']);
            mysqli_stmt_execute($stmt);

            $logType = "LOGOUT_COOKIE";

            if($isProduction){
                setcookie("userSession", NULL, time() - 1, "/", "denvelope.com", true, true);
            }
            else{
                setcookie("userSession", NULL, time() - 1, "/", "");
            }

            mysqli_stmt_close($stmt);
        }
    }
    else{
        $logType = "LOGOUT_NO_COOKIE";
    }

    addLog($logType);
    
    mysqli_close($conn);

    session_unset();
    session_destroy();

    if(isset($_GET['ref'])){
        $ref = $_GET['ref'];

        if($ref != "account" && $ref != "settings" && $ref != "contact" && $ref != "supportcenter" && $ref != "home" && $ref != "adminpanel"){
            header("Location: ../login");
            exit();
        }

        if($ref == "settings"){
            $ref = "account/settings";
        }

        header("Location: ../login/?ref=".$ref);
        exit();
    }

    header("Location: ../");
?>