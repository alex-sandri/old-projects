<?php
    function hexToRGB($hex){
        $rgb = hexdec(substr($hex, 1, 2)).", ".hexdec(substr($hex, 3, 2)).", ".hexdec(substr($hex, 5, 2));

        return $rgb;
    }
?>