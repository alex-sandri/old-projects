<?php
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

        session_start();

        if(isset($_SESSION['emailLength']) || isset($_SESSION['emailCheck'])){
            unset($_SESSION['emailLength']);
            unset($_SESSION['emailCheck']); 
        }

        require("dbh.php");

        $email = trim($_POST['email']);

        if(strlen($email) >= 4){

            $_SESSION['emailLength'] = "longEnough";

            $sqlQuery = "SELECT * FROM users WHERE email=?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                $_SESSION['sqlError'] = true;

                header('Content-type: application/json');

                $JSONdata = array($_SESSION);
                echo json_encode($JSONdata);

                exit();
            }
            else{
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);

                $resultNum = mysqli_stmt_num_rows($stmt);

                if($resultNum > 0){
                    $_SESSION['emailCheck'] = "alreadyTaken";

                    header('Content-type: application/json');

                    $JSONdata = array($_SESSION);
                    echo json_encode($JSONdata);

                    exit();
                }
                else{
                    $_SESSION['emailCheck'] = "available";

                    header('Content-type: application/json');

                    $JSONdata = array($_SESSION);
                    echo json_encode($JSONdata);

                    exit();
                }
            }
        }
        else{
            $_SESSION['emailLength'] = "tooShort";

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