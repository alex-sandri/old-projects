<?php
    
require(dirname(__FILE__, 3) . "/src/autoload.php");

use Denvelope\Models\User;

User::LogOut();

header("Location: ../");
exit();