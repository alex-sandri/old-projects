<?php
    function updateAuthyUserEmail($newEmail){
        require("remove-user.php");
        require("add-user.php");
        require("../php/get-2fa-user.php");

        $user = get2FAUser();

        removeAuthyUser();

        addAuthyUser($newEmail, $user['phoneNumber'], $user['phonePrefix']);
    }
?>