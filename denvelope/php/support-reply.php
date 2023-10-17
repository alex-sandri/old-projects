<?php
    if(isset($_POST['send-reply-button'])){
        session_start();

        require("dbh.php");
        require("std-date.php");
        require("send-email-ses.php");
        require("get-support-case.php");
        require("mark-case-as-closed.php");
        require("get-user-preferred-language.php");

        $_SESSION['replyMessageError'] = false;

        $message = $_POST['message'];
        $caseNumber = $_POST['case-number'];
        $markAsClosed = $_POST['mark-as-closed'];

        $case = getSupportCase($caseNumber);

        $lang = getUserPreferredLanguageByEmail($case['email']);
        require("../lang/".$lang.".php");

        if(!$case){
            header("Location: ../supportcenter");
            exit();
        }

        if(empty($message)){
            $_SESSION['replyMessageError'] = "emptyReplyMessage";
        }
        else if(strlen($message) > 5000){
            $_SESSION['replyMessageError'] = "replyMessageTooLong";
        }

        if($_SESSION['replyMessageError'] != false){
            header("Location: ../supportcenter/?case=".$case['caseNumber']);
            exit();
        }

        if($markAsClosed){
            markCaseAsClosed($caseNumber);
        }

        $sqlQuery = "INSERT INTO support_messages (username, email, caseNumber, body, status, time) VALUES ('support', 'support@denvelope.com', ?, ?, 'sent', ?)";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        $time = stdDate();

        mysqli_stmt_bind_param($stmt, "sss", $caseNumber, $message, $time);
        mysqli_stmt_execute($stmt);

        $subject = getTranslatedContent("email_support_request_reply_subject_reply_to").": ".$case['title'];
        $message .= "\r\n\r\n".getTranslatedContent("email_support_request_reply_case_id").": #".$caseNumber;

        if($markAsClosed){
            $subject = "[".getTranslatedContent("email_support_request_reply_case_closed")."] ".$subject;
            $message .= "\r\n".getTranslatedContent("email_support_request_reply_status_closed");
        }

        sendEmailSES($case['email'], $subject, $message, "");

        $_SESSION['replyMessageSent'] = true;

        header("Location: ../supportcenter");
        exit();
    }
    else{
        header("Location: ../");
        exit();
    }
?>