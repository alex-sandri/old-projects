<?php
    session_start();

    $_SESSION['username'] = "ycdemo";
    $_SESSION['email'] = "ycdemo@denvelope.com";

    header("Location: ../account");
?>