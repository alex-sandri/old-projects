<?php
    if(isset($_POST['contact-button'])){
        session_start();

        require("dbh.php");
        require("base36.php");
        require("std-date.php");
        require("send-email-ses.php");
        require("send-email-support-request-received.php");

        $subject = $_POST['subject'];
        $message = $_POST['message'];

        $_SESSION['contactError'] = false;

        if(empty($subject)){
            $_SESSION['contactSubjectError'] = "emptySubject";
            $_SESSION['contactError'] = true;
        }
        else if(mb_strlen($subject, "utf8") > 100){
            $_SESSION['contactSubjectError'] = "subjectTooLong";
            $_SESSION['contactError'] = true;
        }
        
        if(empty($message)){
            $_SESSION['contactMessageError'] = "emptyMessage";
            $_SESSION['contactError'] = true;
        }
        else if(mb_strlen($message, "utf8") > 5000){
            $_SESSION['contactMessageError'] = "messageTooLong";
            $_SESSION['contactError'] = true;
        }

        if($_SESSION['contactError'] != false){
            header("Location: ../contact");
            exit();
        }

        $sqlQuery = "INSERT INTO support_cases (username, email, caseNumber, title, status, time) VALUES (?, ?, ?, ?, 'open', ?)";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        $caseNumber = base36(10, "support_cases", "caseNumber");
        $time = stdDate();

        mysqli_stmt_bind_param($stmt, "sssss", $_SESSION['username'], $_SESSION['email'], $caseNumber, $subject, $time);
        mysqli_stmt_execute($stmt);

        $sqlQuery = "INSERT INTO support_messages (username, email, caseNumber, body, status, time) VALUES (?, ?, ?, ?, 'sent', ?)";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sssss", $_SESSION['username'], $_SESSION['email'], $caseNumber, $message, $time);
        mysqli_stmt_execute($stmt);

        $message .= "\r\n\r\nSender: ".$_SESSION['username'];
        $message .= "\r\nCase ID: #".$caseNumber;

        sendEmailSES("support@denvelope.com", $subject, $message, "");

        sendEmailSupportRequestReceived($_SESSION['email'], $caseNumber);

        $_SESSION['contactMessageSent'] = true;

        header("Location: ../contact");
        exit();
    }
    else{
        header("Location: ../");
        exit();
    }
?>