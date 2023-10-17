<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

        session_start();

        if(isset($_SESSION['usernameLength']) || isset($_SESSION['usernameCheck'])){
            unset($_SESSION['usernameLength']);
            unset($_SESSION['usernameCheck']); 
        }

        require("dbh.php");

        $username = trim($_POST['username']);

        if(strlen($username) >= 4){

            $_SESSION['usernameLength'] = "longEnough";

            $sqlQuery = "SELECT * FROM users WHERE username=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['sqlError'] = true;

                header('Content-type: application/json');

                $JSONdata = array($_SESSION);
                echo json_encode($JSONdata);

                exit();
            }
            else{
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);

                $resultNum = mysqli_stmt_num_rows($stmt);

                if($resultNum > 0){
                    $_SESSION['usernameCheck'] = "alreadyTaken";

                    header('Content-type: application/json');

                    $JSONdata = array($_SESSION);
                    echo json_encode($JSONdata);

                    exit();
                }
                else{
                    $_SESSION['usernameCheck'] = "available";

                    header('Content-type: application/json');

                    $JSONdata = array($_SESSION);
                    echo json_encode($JSONdata);

                    exit();
                }
            }
        }
        else{
            $_SESSION['usernameLength'] = "tooShort";

            header('Content-type: application/json');

            $JSONdata = array($_SESSION);
            echo json_encode($JSONdata);

            exit();
        }
    }
    else{
        header("Location: ../");
        exit();
    }
?>