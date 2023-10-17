<?php
    function sanitizePath($path){ //escapes the mysql wildcard characters

        $sanitizedPath = str_replace("%", "\%", $path);
        $sanitizedPath = str_replace("_", "\_", $path);

        return $sanitizedPath;
    }
?>