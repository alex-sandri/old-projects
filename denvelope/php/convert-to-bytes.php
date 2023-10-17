<?php
    require_once("file-size-unit-priority.php");

    function convertToBytes($fileSize){
        $fileSizeUnitPriority = fileSizeUnitPriority(substr($fileSize, strlen($fileSize) - 2));
        $fileSizePure = substr($fileSize, 0, strlen($fileSize) - 2);

        for($i = 0; $i < $fileSizeUnitPriority; $i++){
            $fileSizePure *= 1024;
        }

        $fileSizeInBytes = $fileSizePure;

        return $fileSizeInBytes;
    }
?>