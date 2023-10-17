<?php
    function getFileIcon($language){
        $langIcon = explode("||", $language);

        if(strpos($langIcon[0], "-svg") === false){
            if($langIcon[0] != "other"){
                $fileIcon = "devicon-".$langIcon[0]."-plain";
            }
            else{
                if($langIcon[1] == "other_archive"){
                    $fileIcon = "fas fa-file-archive";
                }
                else if($langIcon[1] == "other_audio"){
                    $fileIcon = "fas fa-file-audio";
                }
                else if($langIcon[1] == "other_document"){
                    $fileIcon = "fas fa-file-alt";
                }
                else if($langIcon[1] == "other_image"){
                    $fileIcon = "fas fa-file-image";
                }
                else if($langIcon[1] == "other_video"){
                    $fileIcon = "fas fa-file-video";
                }
                else{
                    $fileIcon = "fas fa-file";
                }
            }
        }
        else{
            $fileIcon = explode("-", $langIcon[0])[0];
        }

        return $fileIcon;
    }
?>