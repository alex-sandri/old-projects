<?php
    require("rename-file-s3.php");

    function renameFolderS3($objectKeys, $newObjectKeys){
        foreach ($objectKeys as $oldObjectKey) {
            $newObjectKey = mysqli_fetch_assoc($newObjectKeys)['pathToThis'];
            renameFileS3($oldObjectKey['pathToThis'], $newObjectKey);
        }
    }
?>