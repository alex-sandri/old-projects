<?php
    header('Content-Type: application/javascript');

    require("../php/global-vars.php");

    require("../lang/".$lang.".php");
?>

function addSourceCode(){
    $("#modal").css("display", "flex");

    if($(window).outerWidth() <= 1200){
        $("#header").css("display", "none");
    }
}

function cancelAddSourceCode(){
    $("#single-file").css("display", "none");
    $("#project").css("display", "none");
    $("#multiple-files").css("display", "none");
    $("#modal-content").css("display", "none");
    $("#modal-select").css("display", "flex");
    $("#modal").css("display", "none");
    $("#source-name").val("");
    $("#language").val(0);
    $("#file-single").val(null);
    $("#source-file-upload-label").html('<i class="fas fa-file-upload"></i> <?php echo getTranslatedContent("account_single_file_add_file"); ?>');
    $("#new-file-form").css("display", "none");
    $("#new-folder-form").css("display", "none");
    $("#source-name-new-folder").val("");
    $("#file-project").val(null);
    $("#source-folder-upload-label").html('<i class="fas fa-folder"></i> <?php echo getTranslatedContent("account_project_add_folder"); ?>');
    $("#source-name-project").val("");
    $("#file-multiple").val(null);
    $("#source-multiple-upload-label").html('<i class="fas fa-copy"></i> <?php echo getTranslatedContent("account_multiple_files_add_files"); ?>');
    $(".modal .folder-info-div").remove();
    $("#language-new-file").val(0);
    $("#source-name-new-file").val("");
    $(".error-input-field").html("");
    $(".modal .source-code-info-div").remove();
    $("#source-code-rename-div").css("display", "none");
    $("#folder-rename-div").css("display", "none");

    if($(window).outerWidth() <= 1200){
        $("#header").css("display", "flex");
        $("#modal-select").css("display", "flex");
    }
}

function singleFile(){
    $("#modal-select").css("display", "none");
    $("#modal-content").css("display", "flex");
    $("#single-file").css("display", "flex");
}

function project(){
    $("#modal-select").css("display", "none");
    $("#modal-content").css("display", "flex");
    $("#project").css("display", "flex");
}

function multipleFiles(){
    $("#modal-select").css("display", "none");
    $("#modal-content").css("display", "flex");
    $("#multiple-files").css("display", "flex");
}

function back(){
    $("#single-file").css("display", "none");
    $("#project").css("display", "none");
    $("#multiple-files").css("display", "none");
    $("#modal-content").css("display", "none");
    $("#modal-select").css("display", "flex");
    $("#source-name").val("");
    $("#language").val(0);
    $("#file-single").val(null);
    $("#source-file-upload-label").html('<i class="fas fa-file-upload"></i> <?php echo getTranslatedContent("account_single_file_add_file"); ?>');
    $("#file-upload-name").html("");
    $("#new-file-form").css("display", "none");
    $("#new-folder-form").css("display", "none");
    $("#source-name-new-folder").val("");
    $("#file-project").val(null);
    $("#language-project").val(0);
    $("#source-folder-upload-label").html('<i class="fas fa-folder"></i> <?php echo getTranslatedContent("account_project_add_folder"); ?>');
    $("#source-name-project").val("");
    $("#file-multiple").val(null);
    $("#source-multiple-upload-label").html('<i class="fas fa-copy"></i> <?php echo getTranslatedContent("account_multiple_files_add_files"); ?>');
    $(".error-input-field").html("");

    if($(window).outerWidth() <= 1200){
        $("#modal-select").css("display", "flex");
    }
}

$(window).resize(function(){
    if($("#single-file").css("display") == "none" && $("#project").css("display") == "none" && $("#multiple-files").css("display") == "none" && $("#new-file-form").css("display") == "none" && $("#new-folder-form").css("display") == "none" && $(".source-code-info-div").css("display") == "none" && $("#source-code-rename-div").css("display") == "none" && $("#folder-rename-div").css("display") == "none"){
        $("#modal-select").css("display", "flex");
    }
    else{
        if($("#single-file").css("display") != "none"){
            $("#single-file").css("display", "flex");
        }
        else if($("#project").css("display") != "none"){
            $("#project").css("display", "flex");
        }
        else if($("#multiple-files").css("display") != "none"){
            $("#multiple-files").css("display", "flex");
        }
        else if($("#new-file-form").css("display") != "none"){
            $("#new-file-form").css("display", "flex");
        }
        else if($("#new-folder-form").css("display") != "none"){
            $("#new-folder-form").css("display", "flex");
        }
        else if($("#source-code-rename-div").css("display") != "none"){
            $("#source-code-rename-div").css("display", "flex");
        }
        else if($("#folder-rename-div").css("display") != "none"){
            $("#folder-rename-div").css("display", "flex");
        }
    }
});