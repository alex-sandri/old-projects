<?php
    // Disabled
    if(isset($_POST['two-factor-auth-button']) || false){
        session_start();

        require("api-key.php");
        require("../php/dbh.php");

        $phoneNumber = $_POST['phone-number'];
        $countryCode = $_POST['null']; //I hate this but it's Authy's fault

        $authyAPI = authyAPI();

        $user = $authyAPI->registerUser($_SESSION['email'], $phoneNumber, $countryCode);

        if($user->ok()){
            $sqlQuery = "INSERT INTO authy_users (username, email, authyID, phoneNumber, phonePrefix) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                echo 'An error occurred while processing the request';
                exit();
            }
    
            $userID = $user->id();

            mysqli_stmt_bind_param($stmt, "sssss", $_SESSION['username'], $_SESSION['email'], $userID, $phoneNumber, $countryCode);
            mysqli_stmt_execute($stmt);
        }
        else{
            foreach($user->errors() as $field => $message) {
                printf("$field = $message");
            }
        }

        header("Location: ../account/settings/#security");
        exit();
    }
    
    function addAuthyUser($email, $phoneNumber, $countryCode){
        require_once("api-key.php");

        $authyAPI = authyAPI();

        $user = $authyAPI->registerUser($email, $phoneNumber, $countryCode);
    }
?>