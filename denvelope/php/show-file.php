<?php
    function showFile($file, $shared, $inSharedFolder){
        require("get-file-content-s3.php");
        require("get-object-url-s3.php");

        if(!isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
            require("../php/translate-from-location.php");
        }

        require("../php/global-vars.php");

        require_once("../lang/".$lang.".php");

        $betaHide = true; //DISABLE NOT YET READY FEATURES

        $fileLang = explode("||", $file['language']);

        if($fileLang[0] != "other" || $fileLang[1] == "plain_text"){
            echo '
                <div class="file-save-success-msg" id="file-save-success-msg">
                    <p><i class="fas fa-check"></i> '; echo getTranslatedContent("account_view_source_message_file_saved"); echo'</p>
                </div>
                <div class="file-save-success-msg" id="file-save-error-msg">
                    <p><i class="fas fa-times"></i> '; echo getTranslatedContent("account_view_source_message_error_file_save"); echo'</p>
                </div>
                <div class="view-source-head">
                    <h1 class="view-source-h1">'; echo htmlspecialchars($file['name']); echo'</h1>';
                    if(!$shared || ($shared && $inSharedFolder)){
                        echo '<button class="source-code-button" id="view-source-close-button"><i class="fas fa-times"></i> '; echo getTranslatedContent("account_view_source_close"); echo'</button>';
                    }
                echo'</div>
                <div class="view-source-code" id="view-source-code">';
                    $fileContent = getFileContent($file['pathToThis']);
                                            
                    if(strpos($fileContent, "DENVELOPE_IGNORE") === false){
                        echo htmlspecialchars($fileContent);
                    }
                    else{
                        echo htmlspecialchars(getTranslatedContent("account_view_source_message_ignore"));
                    }
                echo '</div>
                <div class="view-source-bottom">
                    <div class="view-source-bottom-left">
                        <button class="source-code-button" id="view-source-info-button"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_view_source_info"); echo'</button>
                        <button class="source-code-button" id="view-source-share-button"><i class="fas fa-share-alt"></i> '; echo getTranslatedContent("account_view_source_share"); echo'</button>';
                        if(!$betaHide && $shared){
                            echo '
                                <button class="source-code-button" id="view-source-save-to-my-button"><i class="fas fa-save"></i> '; echo getTranslatedContent("account_view_source_save_to_my_account"); echo'</button>
                            ';
                        }
                        echo '
                            <button class="source-code-button" id="view-source-download-button"><a href="../download/?sid='; echo $file['fileID']; echo'" style="display: none;" download></a><i class="fas fa-download"></i> '; echo getTranslatedContent("account_view_source_download"); echo'</button>
                        ';
                        if(!$shared){
                            echo '
                                <button class="source-code-button" id="view-source-edit-button"><i class="fas fa-edit"></i> '; echo getTranslatedContent("account_view_source_edit"); echo'</button>
                            ';
                        }
                        echo '
                            <input type="hidden" id="'; echo "https://denvelope.com/share?sid=".$file['fileID']; echo'"></input>
                        ';
                    echo'</div>';
                    if(!$shared){
                        echo '<div class="view-source-bottom-right">
                                <button class="source-code-button" id="view-source-delete-button"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_view_source_delete"); echo'</button>
                            </div>
                        ';
                    }
                    echo'<div class="view-source-bottom-menu-mob">
                        <button class="source-code-button" id="view-source-bottom-menu-button" style="position: absolute; bottom: 0;"><i class="fas fa-ellipsis-h"></i></button>
                        <div id="view-source-menu-mob" style="display: none;">
                            <button class="source-code-button" id="view-source-info-button-mob"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_view_source_info"); echo'</button>
                            <button class="source-code-button" id="view-source-share-button-mob"><i class="fas fa-share-alt"></i> '; echo getTranslatedContent("account_view_source_share"); echo'</button>
                            <button class="source-code-button" id="view-source-download-button-mob"><a href="../download/?sid='; echo $file['fileID']; echo'" style="display: none;" download></a><i class="fas fa-download"></i> '; echo getTranslatedContent("account_view_source_download"); echo'</button>
                            '; 
                            if(!$shared){
                                echo '<button class="source-code-button" id="view-source-edit-button-mob"><i class="fas fa-edit"></i> '; echo getTranslatedContent("account_view_source_edit"); echo'</button>
                                    <button class="source-code-button" id="view-source-delete-button-mob"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_view_source_delete"); echo'</button>
                                ';
                            }
                            else if(!$betaHide){
                                echo '<button class="source-code-button" id="view-source-save-to-my-button-mob"><i class="fas fa-save"></i> '; echo getTranslatedContent("account_view_source_save_to_my_account"); echo'</button>';
                            }
                        ; echo'
                        </div>
                    </div>
                </div>
                <div class="view-source-modal" id="view-source-modal">
                    <div>
                        <strong>'; echo getTranslatedContent("account_view_source_info_name"); echo': </strong><small>'; echo htmlspecialchars($file['name']); echo'</small>
                    </div>
                    <hr class="folder-info-hr">
                    <div>
                        <strong>'; echo getTranslatedContent("account_view_source_info_created"); echo': </strong><small>'; echo $file['created']; echo'</small>
                    </div>
                    <hr class="folder-info-hr">
                    <div>
                        <strong>'; echo getTranslatedContent("account_view_source_info_last_modified"); echo': </strong><small>'; echo $file['lastModified']; echo'</small>
                    </div>
                    ';
                    if($fileLang[0] != "other"){
                        echo '<hr class="folder-info-hr">
                            <div>
                                <strong>'; echo getTranslatedContent("account_view_source_info_language"); echo': </strong><small>'; $lang = explode("||", $file['language']); echo $lang[2]; echo'</small>
                            </div>
                        ';
                    }
                    else{
                        echo '<hr class="folder-info-hr">
                            <div>
                                <strong>'; echo getTranslatedContent("account_view_source_info_type"); echo': </strong><small>'; $lang = explode("||", $file['language']); echo getTranslatedContent("account_file_language_".$lang[2]); echo'</small>
                            </div>';
                    }
                    echo'
                    <hr class="folder-info-hr">
                    <div>
                        <strong>'; echo getTranslatedContent("account_view_source_info_size"); echo': </strong><small>'; echo $file['size']; echo'</small>
                    </div>
                    <button id="view-source-info-close"><i class="fas fa-times"></i> '; echo getTranslatedContent("account_view_source_info_close"); echo'</button>
                </div>
                <script>
                    var editor = ace.edit("view-source-code");
                    editor.setTheme("ace/theme/dracula");
                    if($(window).width() > 1200){
                        $(".ace_editor").css("height", "calc(100vh - " + ($(".view-source-head").outerHeight(true) + $(".view-source-bottom").outerHeight(true)) + "px - 2vw - 2vw)");
                    }
                    else{
                        $(".ace_editor").css("height", "calc(100% - " + ($(".view-source-head").outerHeight(true) + $("#view-source-bottom-menu-button").outerHeight(true)) + "px - 0vw - 2vw)");
                    }
                    $(".ace_content").css("background-color", "var(--body-color)");
                    $(".ace_gutter").css("background-color", "var(--body-color)");
                    $(".ace_gutter").css("color", "var(--text-color)");
                    editor.session.setMode("ace/mode/'; $lang = explode("||", $file['language']); echo $lang[1]; echo'");
                    editor.setOptions({
                        printMarginColumn: -1,
                        highlightActiveLine: false,
                        highlightSelectedWord: false,
                        hScrollBarAlwaysVisible: false,
                        vScrollBarAlwaysVisible: false,
                        highlightGutterLine: false,
                        fixedWidthGutter: true,
                        enableBasicAutocompletion: true,
                        enableLiveAutocompletion: true,
                        displayIndentGuides: false,
                        readOnly: true,
                        fontSize: "15px",
                        wrap: false,
                    });

                    $(window).resize(function(){
                        if($(window).width() > 1200){
                            $(".ace_editor").css("height", "calc(100vh - " + ($(".view-source-head").outerHeight(true) + $(".view-source-bottom").outerHeight(true)) + "px - 2vw - 2vw)");
                        }
                        else{
                            $(".ace_editor").css("height", "calc(100% - " + ($(".view-source-head").outerHeight(true) + $("#view-source-bottom-menu-button").outerHeight(true)) + "px - 0vw - 2vw)");
                        }
                    });
                </script>
            ';
        }
        else{
            echo '<script>$("#modal-view-source").css("display", "block")</script>';
            echo '<div class="view-source-head" id="no-source">
                    <h1 class="view-source-h1">'; echo htmlspecialchars($file['name']); echo'</h1>';
                    if(!$shared || ($shared && $inSharedFolder)){
                        echo '<button class="source-code-button" id="view-source-close-button"><i class="fas fa-times"></i> '; echo getTranslatedContent("account_view_source_close"); echo'</button>';
                    }
                echo'</div>
            ';
            if($fileLang[1] == "other_image"){
                echo '<img src="../get-img/?id='; echo $file['fileID']; echo'" alt="'; echo htmlspecialchars($file['name']); echo'" class="img-view" style="margin-top: 1vw;">';
            }
            else{
                echo '<div class="empty-folder-div">
                        <h3 style="margin-bottom: 0;">'; echo getTranslatedContent("account_view_source_source_not_available"); echo'</h3>
                        <img src="../img/humaaans/sitting-8.svg" alt="" style="max-width: 100%;">
                    </div>
                ';
            }
            echo '<div class="view-source-bottom">
                    <div class="view-source-bottom-left">
                        <button class="source-code-button" id="view-source-info-button"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_view_source_info"); echo'</button>
                        <button class="source-code-button" id="view-source-share-button"><i class="fas fa-share-alt"></i> '; echo getTranslatedContent("account_view_source_share"); echo'</button>
                        <input type="hidden" id="'; echo "https://denvelope.com/share?sid=".$file['fileID']; echo'"></input>';
                        if(!$betaHide && $shared){
                            echo '
                                <button class="source-code-button" id="view-source-save-to-my-button"><i class="fas fa-save"></i> '; echo getTranslatedContent("account_view_source_save_to_my_account"); echo'</button>
                            ';
                        }
                        echo '
                            <button class="source-code-button" id="view-source-download-button"><a href="../download/?sid='; echo $file['fileID']; echo'" style="display: none;" download></a><i class="fas fa-download"></i> '; echo getTranslatedContent("account_view_source_download"); echo'</button>
                        ';
                        if($fileLang[1] != "other_archive" && $fileLang[1] != "other_audio" && $fileLang[1] != "other_document" && $fileLang[1] != "other_image" && $fileLang[1] != "other_video"){
                            echo '
                                <button class="source-code-button" id="view-source-edit-button"><i class="fas fa-edit"></i> '; echo getTranslatedContent("account_view_source_edit"); echo'</button>
                            ';
                        }
                        echo '
                    </div>
                    ';
                        if(!$shared){
                            echo '<div class="view-source-bottom-right">
                                    <button class="source-code-button" id="view-source-delete-button"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_view_source_delete"); echo'</button>
                                </div>
                            ';
                        }
                    echo'
                </div>
                <div class="view-source-bottom-menu-mob" id="bottom-menu-mob-no-source">
                    <button class="source-code-button" id="view-source-bottom-menu-button" style="position: absolute; bottom: 0;"><i class="fas fa-ellipsis-h"></i></button>
                    <div id="view-source-menu-mob" style="display: none;">
                        <button class="source-code-button" id="view-source-info-button-mob"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_view_source_info"); echo'</button>
                        <button class="source-code-button" id="view-source-share-button-mob"><i class="fas fa-share-alt"></i> Share</button>
                        <button class="source-code-button" id="view-source-download-button-mob"><a href="../download/?sid='; echo $file['fileID']; echo'" style="display: none;" download></a><i class="fas fa-download"></i> '; echo getTranslatedContent("account_view_source_download"); echo'</button>
                        ';
                            if(!$shared){
                                echo '<button class="source-code-button" id="view-source-delete-button-mob"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_view_source_delete"); echo'</button>';
                            }
                            else if(!$betaHide){
                                echo '<button class="source-code-button" id="view-source-save-to-my-button-mob"><i class="fas fa-save"></i> '; echo getTranslatedContent("account_view_source_save_to_my_account"); echo'</button>';
                            }
                        echo'
                    </div>
                </div>
                <div class="view-source-modal" id="view-source-modal">
                    <div>
                        <strong>'; echo getTranslatedContent("account_view_source_info_name"); echo': </strong><small>'; echo htmlspecialchars($file['name']); echo'</small>
                    </div>
                    <hr class="folder-info-hr">
                    <div>
                        <strong>'; echo getTranslatedContent("account_view_source_info_created"); echo': </strong><small>'; echo $file['created']; echo'</small>
                    </div>
                    <hr class="folder-info-hr">
                    <div>
                        <strong>'; echo getTranslatedContent("account_view_source_info_last_modified"); echo': </strong><small id="last-modified-file">'; echo $file['lastModified']; echo'</small>
                    </div>
                    <hr class="folder-info-hr">
                    <div>
                        <strong>'; echo getTranslatedContent("account_view_source_info_type"); echo': </strong><small>'; $lang = explode("||", $file['language']); echo getTranslatedContent("account_file_language_".$lang[2]); echo'</small>
                    </div>
                    <hr class="folder-info-hr">
                    <div>
                        <strong>'; echo getTranslatedContent("account_view_source_info_size"); echo': </strong><small>'; echo $file['size']; echo'</small>
                    </div>
                    <button id="view-source-info-close"><i class="fas fa-times"></i> '; echo getTranslatedContent("account_view_source_info_close"); echo'</button>
                </div>
            ';
            echo '<script>
                    if($(window).width() > 1200){
                        $(".img-view").css("max-height", "calc(100vh - " + ($(".view-source-head").outerHeight(true) + $(".view-source-bottom").outerHeight(true)) + "px - 2vw - 2vw)");
                    }
                    else{
                        $(".img-view").css("max-height", "calc(100% - " + ($(".view-source-head").outerHeight(true) + $(".view-source-bottom").outerHeight(true)) + "px - 0vw - 2vw)");
                    }
                    $(".img-view").css("max-width", "100%");
                </script>
            ';
        }

        ?>

        <script>
            $(document).ready(function(){
                <?php
                    require("dbh.php");

                    if($shared){
                        echo'
                        $("#view-source-close-button").click(function(){';
                            
                                $sqlQuery = "SELECT * FROM files WHERE fileID=?";
                                $stmt = mysqli_stmt_init($conn);

                                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                    echo '';

                                    exit();
                                }

                                mysqli_stmt_bind_param($stmt, "s", $_GET['iid']);
                                mysqli_stmt_execute($stmt);

                                $result = mysqli_stmt_get_result($stmt);
                                $file = mysqli_fetch_assoc($result);

                                if(count(explode("/", $file['folder'])) > 3){
                                    $sqlQuery = "SELECT * FROM folders WHERE pathToThis=?";
                                    $stmt = mysqli_stmt_init($conn);

                                    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                        echo '';

                                        exit();
                                    }

                                    mysqli_stmt_bind_param($stmt, "s", $file['folder']);
                                    mysqli_stmt_execute($stmt);

                                    $result = mysqli_stmt_get_result($stmt);
                                    $folder = mysqli_fetch_assoc($result);

                                    echo 'window.location.href = "./?fid='.$_GET['fid']."&iid="; echo $folder['folderID']; echo "&t=f".'";';
                                }
                                else{
                                    echo 'window.location.href = "./"';
                                }
                            

                            ;echo'
                        });';
                    }
                ?>

                $(".view-source-bottom-menu-mob").click(function(){
                    if($("#view-source-menu-mob").css("display") == "none"){
                        $("#view-source-menu-mob").css("display", "block");
                    }
                    else{
                        $("#view-source-menu-mob").css("display", "none");
                    }
                });

                $("#view-source-share-button").click(function(){
                    var temp = $("<input></input>");
                    $("#body").append(temp);
                    temp.val($(this).parent().children("input").attr("id")).select();
                    document.execCommand("copy");
                    temp.remove();
                    $("#success-msg p").html("<i class='fas fa-link'></i> <?php echo getTranslatedContent("account_message_link_copied_to_clipboard"); ?>");
                    $("#success-msg").css("display", "block");
                    setTimeout(function(){
                        $("#success-msg").css("display", "none")
                    }, 2500);
                });

                $("#view-source-share-button-mob").click(function(){
                    var temp = $("<input></input>");
                    $("#body").append(temp);

                    if($(this).parent().parent().attr("id") == "bottom-menu-mob-no-source"){
                        temp.val($(this).parent().parent().prev().children(".view-source-bottom-left").children("input").attr("id")).select();
                    }
                    else{
                        temp.val($(this).parent().parent().parent().children(".view-source-bottom-left").children("input").attr("id")).select();
                    }

                    document.execCommand("copy");
                    temp.remove();
                    $("#success-msg p").html("<i class='fas fa-link'></i> <?php echo getTranslatedContent("account_message_link_copied_to_clipboard"); ?>");
                    $("#success-msg").css("display", "block");
                    setTimeout(function(){
                        $("#success-msg").css("display", "none")
                    }, 2500);
                });

                $("#view-source-download-button").click(function(){
                    $(this).children("a")[0].click();
                });

                $("#view-source-download-button-mob").click(function(){
                    $(this).children("a")[0].click();
                });

                $("#view-source-info-button, #view-source-info-button-mob").click(function(){
                    if($(".view-source-modal").css("display") == "none"){
                        $(".view-source-head").css("width", "calc(70% - 1vw + 2px)");
                        $(".ace_editor").css("width", "calc(70% - 1vw + 2px)");
                        $(".view-source-bottom").css("width", $(".view-source-head").outerWidth(true));
                        $(".view-source-modal").css("display", "block");
                        $("#source-not-available").css("width", "calc(70% - 1vw + 2px)");

                        if($(window).width() <= 900){
                            $(".view-source-head").css("display", "none");
                            $(".ace_editor").css("display", "none");
                            $(".view-source-bottom").css("display", "none");
                        }
                    }
                    else{
                        $(".view-source-head").css("width", "100%");
                        $(".ace_editor").css("width", "100%");
                        $(".view-source-bottom").css("width", "calc(100% - 2vw - 4px)");
                        $(".view-source-modal").css("display", "none");
                        $("#source-not-available").css("width", "100%");
                    }
                });

                $(window).resize(function(){
                    $(".view-source-bottom").css("width", $(".view-source-head").outerWidth(true));
                });

                $("#view-source-info-close").click(function(){
                    $(".view-source-head").css("width", "100%");
                    $(".ace_editor").css("width", "100%");
                    $(".view-source-head").css("display", "flex");
                    $(".ace_editor").css("display", "block");
                    $(".view-source-bottom").css("display", "block");
                    $(".view-source-bottom").css("width", "calc(100% - 2vw - 4px)");
                    $(".view-source-modal").css("display", "none");
                    $("#source-not-available").css("width", "100%");
                });
            });
        </script>

<?php
    }
?>