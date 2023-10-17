<?php
    function verifyAuthyToken($token, $usernameEmail){
        require("api-key.php");
        require("../php/dbh.php");

        $authyAPI = authyAPI();
        
        $sqlQuery = "SELECT * FROM authy_users WHERE username=? OR email=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $usernameEmail, $usernameEmail);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        $verification = $authyAPI->verifyToken($user['authyID'], $token, array("force" => "true"));

        if($verification->ok()){
            $validToken = true;
        }
        else{
            $validToken = false;
        }

        return $validToken;
    }
?>