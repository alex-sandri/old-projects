<?php
    require("is-production.php"); //restricted to only have $isProduction, because other files requiring this in global-vars could lead to 500

    if($isProduction){
        $DB_SERVER = $_SERVER['RDS_HOSTNAME'];
        $DB_USERNAME = $_SERVER['RDS_USERNAME'];
        $DB_PASSWORD = $_SERVER['RDS_PASSWORD'];
        $DB_NAME = $_SERVER['RDS_DB_NAME'];
        $DB_PORT = $_SERVER['RDS_PORT'];
    }
    else{
        $DB_SERVER = "localhost";
        $DB_USERNAME = "root";
        $DB_PASSWORD = "";
        $DB_NAME = "denvelope";
        $DB_PORT = 3306;
    }

    $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME, $DB_PORT);

    if(!$conn){
        die("ERROR: Connection Failed. ".mysqli_connect_error());
    }
?>