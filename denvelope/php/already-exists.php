<?php
    function exists($path, $name, $table, $id = "PLACEHOLDER_ID"){
        require("dbh.php");
        require_once("sanitize-path.php");

        $newPath = $path;
        $newName = $name;

        $tableID = $table == "files" ? "fileID" : "folderID";

        $sqlQuery = "SELECT * FROM $table WHERE pathToThis=? AND $tableID!=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $path, $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $element = mysqli_fetch_assoc($result);

        if($element){
            $path = sanitizePath($path);
            $path .= "/%";
            $nameMatch =  sanitizePath($name)." (%)";

            $sqlQuery = "SELECT * FROM $table WHERE pathToThis LIKE ? AND name LIKE ? ORDER BY name";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                echo 'An error occurred while processing the request';
                exit();
            }

            mysqli_stmt_bind_param($stmt, "ss", $path, $nameMatch);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $copyNumFinal = 1;

            foreach ($result as $element) {
                $copyNum = substr(substr($element['name'], strpos($element['name'], "(") + 1), 0, strpos(substr($element['name'], strpos($element['name'], "(")), ")") - 1);

                if(is_numeric($copyNum) && $copyNum == $copyNumFinal){
                    $copyNumFinal = $copyNum + 1;
                }
            }

            if(strpos($name, ".") && $table == "files"){
                $newName = substr($name, 0, strripos($name, "."))." ($copyNumFinal).".substr($name, strripos($name, ".") + 1);
            }
            else{
                $newName .= " ($copyNumFinal)";
            }

            $newPath = substr($newPath, 0, strripos($newPath, $name)).$newName;
        }

        return array(
            $newPath,
            $newName,
        );
    }
?>