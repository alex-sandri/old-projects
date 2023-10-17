<?php
    function betterSize($fileSize){

        for($i = 0; $fileSize >= 1024; $i++){
            $fileSize /= 1024;
        }

        $fileSize = round($fileSize);

        switch ($i) {
            case 0:
                $betterSize = $fileSize."B";
                break;
            case 1:
                $betterSize = $fileSize."KB";
                break;
            case 2:
                $betterSize = $fileSize."MB";
                break;
            case 3:
                $betterSize = $fileSize."GB";
                break;
            case 4:
                $betterSize = $fileSize."TB";
                break;
            case 5:
                $betterSize = $fileSize."PB";
                break;
            case 6:
                $betterSize = $fileSize."EB"; //exabyte
                break;
            case 7:
                $betterSize = $fileSize."ZB"; //zettabyte
                break;
            case 8:
                $betterSize = $fileSize."YB"; //yottabyte
                break;
        }

        return $betterSize;
    }
?>