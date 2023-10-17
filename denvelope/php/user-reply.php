<?php
    if(isset($_POST['send-reply-button']) && isset($_POST['message']) && isset($_POST['case-number'])){
        session_start();

        require("dbh.php");
        require("std-date.php");
        require("send-email-ses.php");
        require("get-support-case.php");
        require("get-user-preferred-language.php");

        $_SESSION['replyMessageError'] = false;

        $message = $_POST['message'];
        $caseNumber = $_POST['case-number'];

        $case = getSupportCase($caseNumber);

        $lang = getUserPreferredLanguageByEmail($case['email']);
        require("../lang/".$lang.".php");

        if(!$case){
            header("Location: ../account/settings/#support");
            exit();
        }

        if(empty($message)){
            $_SESSION['replyMessageError'] = "emptyReplyMessage";
        }
        else if(strlen($message) > 5000){
            $_SESSION['replyMessageError'] = "replyMessageTooLong";
        }

        if($_SESSION['replyMessageError'] != false){
            header("Location: ../account/settings/#support");
            exit();
        }

        $sqlQuery = "INSERT INTO support_messages (username, email, caseNumber, body, status, time) VALUES (?, ?, ?, ?, 'sent', ?)";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        $time = stdDate();

        mysqli_stmt_bind_param($stmt, "sssss", $_SESSION['username'], $_SESSION['email'], $caseNumber, $message, $time);
        mysqli_stmt_execute($stmt);

        $subject = getTranslatedContent("email_support_request_reply_subject_reply_to").": ".$case['title'];
        $message .= "\r\n\r\n".getTranslatedContent("email_support_request_reply_case_id").": #".$caseNumber;
        $message .= "\r\n\r\n".getTranslatedContent("email_support_request_reply_sender").": ".$case['username'];

        sendEmailSES("support@denvelope.com", $subject, $message, "");

        $_SESSION['replyMessageSent'] = true;

        header("Location: ../account/settings/#support");
        exit();
    }
    else{
        header("Location: ../");
        exit();
    }
?>