<?php
    if(isset($_POST['support-case-id']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        $caseNumber = $_POST['support-case-id'];

        $messages = getSupportMessages($caseNumber);

        header("Content-Type: application/json");

        $messagesArray = array();

        foreach ($messages as $message) {
            array_push($messagesArray, $message);
        }

        echo json_encode(array(
            "supportMessages" => $messagesArray,
        ));

        exit();
    }

    function getSupportMessages($caseNumber){
        require("dbh.php");

        $sqlQuery = "SELECT * FROM support_messages WHERE caseNumber=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }
        
        mysqli_stmt_bind_param($stmt, "s", $caseNumber);
        mysqli_stmt_execute($stmt);

        $supportMessages = mysqli_stmt_get_result($stmt);

        return $supportMessages;
    }
?>