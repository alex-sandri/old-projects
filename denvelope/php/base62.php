<?php
    function base62($strLength, $table, $tableID){
        require("dbh.php");

        $charset = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $uuid = "";

        for($i = 0; $i < $strLength; $i++){
            $uuid .= $charset[mt_rand(0, 61)];
        }

        $sqlQuery = "SELECT * FROM $table WHERE $tableID=?";
        $stmtBase = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmtBase, $sqlQuery)){
            $sqlError = "sqlError";
            
            header("Location: ../");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmtBase, "s", $uuid);
            mysqli_stmt_execute($stmtBase);
            mysqli_stmt_store_result($stmtBase);

            $resultNum = mysqli_stmt_num_rows($stmtBase);

            if($resultNum > 0){
                $uuid = base62($strLength, $table, $tableID);
            }
        }

        return $uuid;
    }
?>