<?php
    require("../php/dbh.php");
    require("../php/send-email-ses.php");

    $sqlQuery = "SELECT * FROM users";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
        echo "An error occurred while processing the request";
        exit();
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $plaintext_body = "Ciao,\r\n\r\n";
    $plaintext_body .= "ti scriviamo per informarti che dal 15 Febbraio 2020 non potrai più accedere al tuo account Denvelope a causa di un cambio di infrastruttura in concomitanza con il rilascio della nuova versione, saremo costretti quindi a chiudere il tuo account ed eliminarne tutti i dati.\r\n\r\n";
    $plaintext_body .= "Potrai successivamente creare un nuovo account a partire dalla data sopra riportata.\r\n\r\n";
    $plaintext_body .= "Ti consigliamo quindi di scaricare tutti i tuoi file prima della data sopra riportata, altrimenti ne perderai l'accesso e non sarai più in grado di recuperarli.\r\n\r\n";
    $plaintext_body .= "Cordiali saluti,\r\n";
    $plaintext_body .= "Il Team Denvelope.";

    $i = 0;

    foreach ($result as $user)
    {
        $to = $user["email"];

        sendEmailSES($to, "Chiusura account", $plaintext_body, "");

        if ($i % 5 === 0) sleep(1);

        $i++;
    }
?>