<?php
    function fileSizeUnitPriority($unit)
    {
        switch ($unit) {
            case "B ":
                $priority = 0;
                break;
            case "KB":
                $priority = 1;
                break;
            case "MB":
                $priority = 2;
                break;
            case "GB":
                $priority = 3;
                break;
            case "TB":
                $priority = 4;
                break;
            case "PB":
                $priority = 5;
                break;
            case "EB":
                $priority = 6;
                break;
            case "ZB":
                $priority = 7;
                break;
            case "YB":
                $priority = 8;
                break;
        }

        return $priority;
    }
?>