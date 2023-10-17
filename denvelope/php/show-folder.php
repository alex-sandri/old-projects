<?php
    function showFolder($folderID){
        session_start();

        if(isset($_GET['order'])){
            $order = $_GET['order'];
    
            if($order != "name" && $order != "date" && $order != "size" && $order != "language" && $order != "last-modified"){
                header("Location: ../");
                exit();
            }
        }
    
        if(isset($_GET['d']) && $_GET['d'] != "asc" && $_GET['d'] != "desc"){
            header("Location: ../");
            exit();
        }

        $isSharedHeader = true;
        $betaDisableAccessHeader = false;
        
        require("../php/dbh.php");
        require("../php/get-num-of-files-in-folder.php");
        require("../php/get-num-of-folders-in-folder.php");
        require_once("../php/get-folder.php");

        if(!isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
            require("../php/translate-from-location.php");
        }

        require("../php/global-vars.php");
        
        require("../php/header.php");

        require_once("../lang/".$lang.".php");

        if(isset($_GET['order'])){
            $order = $_GET['order'];

            if($order == "date"){
                $order = "createdUnixTime";
            }
            else if($order == "size"){
                $order = "sizeInBytes";
            }
            else if($order == "language"){
                $order = "name";
            }
            else if($order == "last-modified"){
                $order = "lastModifiedUnixTime";
            }
        }

        if(isset($_GET['d'])){
            $d = strtoupper($_GET['d']);
        }
        else{
            $d = "ASC";
        }

        $folder = getFolder($folderID);

        if(isset($_GET['order'])){
            $sqlQuery = "SELECT * FROM folders WHERE folder=? ORDER BY ".$order." ".$d;
        }
        else{
            $sqlQuery = "SELECT * FROM folders WHERE folder=? ORDER BY name";
        }

        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }
        
        mysqli_stmt_bind_param($stmt, "s", $folder['pathToThis']);
        mysqli_stmt_execute($stmt);

        $folders = mysqli_stmt_get_result($stmt);

        $i = 0;

?>
    <div id="account-div-for-cm" style="margin: 1vw; height: calc(100% - 1vw); margin-left: 0;">
        <div id="menu-div">
            <button class="add-source-code-button" id="download-shared-folder-button" style="margin-bottom: 0;"><i class="fas fa-download"></i> <?php echo getTranslatedContent("account_folder_buttons_download"); ?></button>
            <a id="shared-folder-download-link" href="../download/?fid=<? echo $folder['folderID']; ?>" style="display: none;" download></a>
            <div class="search-container">
                <select name="search-box-type" id="search-box-type" class="search-box-type">
                    <option value="name" selected><?php echo getTranslatedContent("account_search_by_name"); ?></option>
                    <option value="date"><?php echo getTranslatedContent("account_search_by_date"); ?></option>
                    <option value="size"><?php echo getTranslatedContent("account_search_by_size"); ?></option>
                    <option value="language"><?php echo getTranslatedContent("account_search_by_language"); ?></option>
                    <option value="last-modified"><?php echo getTranslatedContent("account_search_by_last_modified"); ?></option>
                </select>
                <input type="search" class="input search-box" name="search-box" id="search-box" placeholder="&#xf002; <?php echo getTranslatedContent("account_search_box"); ?>..." autocorrect="off" autocapitalize="off" spellcheck="false">
            </div>
            <div id="sort-by-title-container">
                <div class="sort-by-container">
                    <?php
                        if(isset($_GET['order'])){
                            $order = $_GET['order'];

                            if($order == "name" || $order == "language"){
                                if(!isset($_GET['d']) || (isset($_GET['d']) && $_GET["d"] == "asc")){
                                    echo '<button class="order-change-button"><i class="fas fa-sort-alpha-down"></i></button>';
                                }
                                else{
                                    echo '<button class="order-change-button"><i class="fas fa-sort-alpha-up"></i></button>';
                                }
                            }
                            else if($order == "date" || $order == "last-modified"){
                                if(!isset($_GET['d']) || (isset($_GET['d']) && $_GET["d"] == "asc")){
                                    echo '<button class="order-change-button"><i class="fas fa-sort-numeric-down"></i></button>';
                                }
                                else{
                                    echo '<button class="order-change-button"><i class="fas fa-sort-numeric-up"></i></button>';
                                }
                            }
                            else{
                                if(!isset($_GET['d']) || (isset($_GET['d']) && $_GET["d"] == "asc")){
                                    echo '<button class="order-change-button"><i class="fas fa-sort-amount-up"></i></button>';
                                }
                                else{
                                    echo '<button class="order-change-button"><i class="fas fa-sort-amount-down"></i></button>';
                                }
                            }
                        }
                    ?>
                    <select name="sort-by" id="sort-by" class="sort-by">
                        <option value="0" hidden <?php if(!isset($_GET['order'])) echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by"); ?></option>
                        <option value="name" <?php if(isset($_GET['order']) && $_GET['order'] == "name") echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by_name"); ?></option>
                        <option value="date" <?php if(isset($_GET['order']) && $_GET['order'] == "date") echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by_date"); ?></option>
                        <option value="size" <?php if(isset($_GET['order']) && $_GET['order'] == "size") echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by_size"); ?></option>
                        <option value="language" <?php if(isset($_GET['order']) && $_GET['order'] == "language") echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by_language"); ?></option>
                        <option value="last-modified" <?php if(isset($_GET['order']) && $_GET['order'] == "last-modified") echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by_last_modified"); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal" id="modal"></div>
        <hr class="folder-info-hr" style="display: none;">
        <div class="folder-name-back-button-container">
            <h1 class="account-source-codes-h1"><?php echo htmlspecialchars($folder['name']); ?></h1>
            <?php
                if(isset($_GET['iid']) && (isset($_GET['t']) && $_GET['t'] == "f")){
                    echo '<button class="folder-back-button" id="folder-back-button"><i class="fas fa-arrow-left"></i> '; echo getTranslatedContent("account_back"); echo'</button>';
                }
            ?>
        </div>
        <div class="source-codes-container" id="source-codes-container">
            <div style="display: flex; width: 100%; flex-wrap: wrap;" id="folder-container">
<?php

        foreach ($folders as $folder) {
            $foldersInFolder = getNumOfFoldersIn($folder['pathToThis']);
            $filesInFolder = getNumOfFilesIn($folder['pathToThis']);

            echo '<div class="folder-div" id="f-'; echo $i; echo'">
                    <div id='; echo $folder['folderID']; echo' style="display: flex; width: 100%;">
                        <div class="folder-icon">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div class="folder-main-div">
                            <h3 class="folder-h3">'; echo htmlspecialchars($folder['name']); echo'</h3>
                        </div>
                        <div class="folder-menu-button-div">
                            <button class="folder-menu-button"><i class="fas fa-ellipsis-h"></i></button>
                        </div>
                    </div>
                    <div class="folder-info-div">
                        <div>
                            <div>
                                <strong>'; echo getTranslatedContent("account_folder_info_name"); echo': </strong><small sort-data="name-info">'; echo htmlspecialchars($folder['name']); echo'</small>
                            </div>
                            <hr class="folder-info-hr">
                            <div>
                                <strong>'; echo getTranslatedContent("account_folder_info_created"); echo': </strong><small sort-data="date-info">'; echo $folder['created']; echo'</small>
                            </div>
                            <hr class="folder-info-hr">
                            <div>
                                <strong>'; echo getTranslatedContent("account_folder_info_last_modified"); echo': </strong><small sort-data="last-mod-info">'; echo $folder['lastModified']; echo'</small>
                            </div>
                            <hr class="folder-info-hr">
                            <div>
                                <strong>'; echo getTranslatedContent("account_folder_info_size"); echo': </strong><small sort-data="size-info">'; echo $folder['size']; echo'</small>
                            </div>
                            <hr class="folder-info-hr">
                            <div>
                                <strong>'; echo getTranslatedContent("account_folder_info_folders"); echo': </strong><small>'; echo $foldersInFolder; echo'</small>
                            </div>
                            <hr class="folder-info-hr">
                            <div>
                                <strong>'; echo getTranslatedContent("account_folder_info_files"); echo': </strong><small>'; echo $filesInFolder; echo'</small>
                            </div>
                        </div>
                        <button onclick="cancelAddSourceCode()" class="folder-info-button"><i class="fas fa-times"></i> '; echo getTranslatedContent("account_folder_info_close"); echo'</button>
                    </div>
                </div>
                <div class="folder-div-mob-buttons" style="display: none;">
                    <button class="folder-view-button"><i class="fas fa-eye"></i> '; echo getTranslatedContent("account_folder_buttons_view"); echo'</button>
                    <button class="folder-share-button" share-link="https://denvelope.com/share/?fid='; echo $folder['folderID']; echo'"><i class="fas fa-share-alt"></i> '; echo getTranslatedContent("account_folder_buttons_share"); echo'</button>
                    <button class="folder-info-button-menu-mob"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_folder_buttons_info"); echo'</button>
                    <button class="folder-download-button"><a href="../download/?fid='; echo $folder['folderID']; echo'" style="text-decoration: none; color: inherit;" download><i class="fas fa-download"></i> '; echo getTranslatedContent("account_folder_buttons_download"); echo'</a></button>
                </div>
            ';

            $i++;
        }

        $noFolders = false;

        if($i == 0){
            $noFolders = true;
        }
?>
            </div>
<?php

        if(isset($_GET['order'])){
            $order = $_GET['order'];

            if($order == "date"){
                $order = "unixTimeCreated";
            }
            else if($order == "size"){
                $order = "sizeInBytes";
            }
            else if($order == "last-modified"){
                $order = "unixTimeLastModified";
            }
        }

        if(isset($_GET['d'])){
            $d = strtoupper($_GET['d']);
        }
        else{
            $d = "ASC";
        }

        if(isset($_GET['order'])){
            $sqlQuery = "SELECT * FROM files WHERE folder=? ORDER BY ".$order." ".$d; 
        }
        else{
            $sqlQuery = "SELECT * FROM files WHERE folder=? ORDER BY name"; 
        }


        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            echo 'An error occurred while processing the request';
            exit();
        }

        $folder = getFolder($folderID);
                    
        mysqli_stmt_bind_param($stmt, "s", $folder['pathToThis']);
        mysqli_stmt_execute($stmt);

        $files = mysqli_stmt_get_result($stmt);     

        $i = 0;

        foreach ($files as $file) {                        
            echo '<div class="source-code-div" id="scd-'; echo $i."\""; echo'>
                    <div class="source-code-language-icon"'; $lang = explode("||", $file['language']); if($lang[0] == "other") echo 'style="padding-left: calc(10px + 6.25px); padding-right: calc(10px + 6.25px + 2px);" id="other-source-code"'; echo'>
                        <i class="'; echo $file['fileIcon']; echo '"></i>
                    </div>
                    <div class="source-code-main-div">
                        <h3 class="source-code-h3">'; echo htmlspecialchars($file['name']); ; echo'</h3>
                        <h5 class="source-code-h5">
                            <strong>Created: </strong><small>'; echo $file['created'] ; echo'</small>
                            <br>
                            <strong>Last modified: </strong><small>'; echo $file['lastModified']; echo'</small>
                        </h5>
                        <div class="source-code-buttons-div">
                            <button class="source-code-button source-code-view-button"><i class="fas fa-eye"></i> '; echo getTranslatedContent("account_source_code_buttons_view"); echo'</button>
                            <button class="source-code-button source-code-share-button"><i class="fas fa-share-alt"></i> '; echo getTranslatedContent("account_source_code_buttons_share"); echo'</button>
                            <button class="source-code-button source-code-info-button"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_source_code_buttons_info"); echo'</button>
                            <button class="source-code-button source-code-download-button"><a href="../download/?sid='; echo $file['fileID']; echo'" style="text-decoration: none; color: inherit;" download><i class="fas fa-download"></i> '; echo getTranslatedContent("account_source_code_buttons_download"); echo'</a></button>
                        </div>
                        <div class="source-code-share-div">
                            <a href="" id="'; echo $file['fileID']; echo'" class="source-code-share-link">'; echo "https://denvelope.com/share/?sid=".$file['fileID']; echo'</a>
                            <div class="source-code-copy-button">
                                <i class="fas fa-copy"></i> Copy
                            </div>
                        </div>
                        <div class="source-code-info-div">
                            <div>
                                <strong>'; echo getTranslatedContent("account_source_code_info_name"); echo': </strong><small sort-data="name-info">'; echo htmlspecialchars($file['name']); echo'</small>
                            </div>
                            <hr class="folder-info-hr">
                            <div>
                                <strong>'; echo getTranslatedContent("account_source_code_info_created"); echo': </strong><small sort-data="date-info">'; echo $file['created']; echo'</small>
                            </div>
                            <hr class="folder-info-hr">
                            <div>
                                <strong>'; echo getTranslatedContent("account_source_code_info_last_modified"); echo': </strong><small sort-data="last-mod-info">'; echo $file['lastModified']; echo'</small>
                            </div>';
                            $fileLang = explode("||", $file['language']);
                                            
                            if($fileLang[0] != "other"){
                                echo '<hr class="folder-info-hr">
                                    <div>
                                        <strong>'; echo getTranslatedContent("account_source_code_info_language"); echo': </strong><small sort-data="lang-type-info">'; $lang = explode("||", $file['language']); echo $lang[2]; echo'</small>
                                    </div>
                                ';
                            }
                            else{
                                echo '<hr class="folder-info-hr">
                                    <div>
                                        <strong>'; echo getTranslatedContent("account_source_code_info_type"); echo': </strong><small sort-data="lang-type-info">'; $lang = explode("||", $file['language']); echo getTranslatedContent("account_file_language_".$lang[2]); echo'</small>
                                    </div>';
                                }
                            echo'<hr class="folder-info-hr">
                            <div>
                                <strong>'; echo getTranslatedContent("account_source_code_info_size"); echo': </strong><small sort-data="size-info">'; echo trim($file['size']); echo'</small>
                            </div>
                            <button onclick="cancelAddSourceCode()" class="folder-info-button"><i class="fas fa-times"></i> '; echo getTranslatedContent("account_source_code_info_close"); echo'</button>
                        </div>
                    </div>
                    <div class="source-code-buttons-div-mob">
                        <button class="source-code-button"><i class="fas fa-ellipsis-h"></i></button>
                    </div>
                </div>
                <div class="source-code-buttons-div-mob-buttons" style="display: none;">
                    <button class="source-code-view-button"><i class="fas fa-eye"></i> '; echo getTranslatedContent("account_source_code_buttons_view"); echo'</button>
                    <button class="source-code-share-button"><i class="fas fa-share-alt"></i> '; echo getTranslatedContent("account_source_code_buttons_share"); echo'</button>
                    <button class="source-code-info-button"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_source_code_buttons_info"); echo'</button>
                    <button class="source-code-download-button"><a href="../download/?sid='; echo $file['fileID']; echo'" style="text-decoration: none; color: inherit;" download><i class="fas fa-download"></i> '; echo getTranslatedContent("account_source_code_buttons_download"); echo'</a></button>
                </div>
            ';

            $i++;
        }

        $noFiles = false;

        if($i == 0){
            $noFiles = true;
        }


                        
        echo '<div class="empty-folder-div" id="empty-folder-div" '; if(!$noFolders || !$noFiles) echo 'style="display: none;"'; echo'>
                <h3 style="margin-bottom: 0;" id="empty-folder-div-h3">'; echo getTranslatedContent("account_empty_folder"); echo'</h3>
                <img src="../img/humaaans/sitting-8.svg" alt="" style="max-width: 100%;" id="empty-folder-div-img">
            </div>
        ';

        echo '<div class="empty-folder-div" id="no-elements-found-on-search-div" style="display: none;">
                <h3 style="margin-bottom: 0;" id="no-elements-found-on-search-div-h3">'; echo getTranslatedContent("account_no_elements_found_on_search"); echo'</h3>
                <img src="../img/humaaans/sitting-8.svg" alt="" style="max-width: 100%;" id="no-elements-found-on-search-div-img">
            </div>
        ';
?>
        </div>
    </div>

        <div class="account-context-menu">
            <h3 id="source-code-view-button-cm" class="file-related-cm-option"><i class="fas fa-eye"></i> <?php echo getTranslatedContent("account_context_menu_view"); ?></h3>
            <h3 id="source-code-share-button-cm" class="file-related-cm-option"><i class="fas fa-share-alt"></i> <?php echo getTranslatedContent("account_context_menu_share"); ?></h3>
            <h3 id="source-code-info-button-cm" class="file-related-cm-option"><i class="fas fa-info-circle"></i> <?php echo getTranslatedContent("account_context_menu_info"); ?></h3>
            <h3 id="source-code-download-button-cm" class="file-related-cm-option"><i class="fas fa-download"></i> <?php echo getTranslatedContent("account_context_menu_download"); ?></h3>
        </div>

        <div class="plus-button-div">
            <button class="plus-button" id="plus-button">
                <i class="fas fa-bars"></i>
                <i class="fas fa-times"></i>
            </button>
        </div>

        <script>
            $(document).ready(function(){
                $("#plus-button").click(function(){
                    if($("#menu-div").css("display") != "none"){
                        $("#menu-div").css("animation", "100ms menuClose");

                        setTimeout(() => {
                            $("#menu-div").css("display", "none");
                            $("#plus-button i:last-child").css("display", "none");
                            $("#plus-button i:first-child").css("display", "unset");
                        }, 100);

                        if($(window).width() > 1000){
                            $(".folder-name-back-button-container, .source-codes-container").css("left", "0");
                            $(".folder-name-back-button-container, .source-codes-container").css("width", "calc(100% - 1vw)");
                        }
                    }
                    else{
                        $("#menu-div").css("display", "flex");
                        $("#menu-div").css("animation", "100ms menuOpen");

                        setTimeout(() => {
                            $("#plus-button i:first-child").css("display", "none");
                            $("#plus-button i:last-child").css("display", "unset");
                        }, 100);

                        if($(window).width() > 1200){
                            $(".folder-name-back-button-container, .source-codes-container").css("left", "25%");
                            $(".folder-name-back-button-container, .source-codes-container").css("width", "calc(75% - 1vw)");
                        }
                        else if($(window).width() > 1000){
                            $(".folder-name-back-button-container, .source-codes-container").css("left", "30%");
                            $(".folder-name-back-button-container, .source-codes-container").css("width", "calc(70% - 1vw)");
                        }
                    }
                });

                $(window).resize(function(){
                    if($(window).width() > 1200){
                        $(".folder-name-back-button-container, .source-codes-container").css("left", "25%");
                        $(".folder-name-back-button-container, .source-codes-container").css("width", "calc(75% - 1vw)");
                    }
                    else if($(window).width() > 1000){
                        $(".folder-name-back-button-container, .source-codes-container").css("left", "30%");
                        $(".folder-name-back-button-container, .source-codes-container").css("width", "calc(70% - 1vw)");
                    }
                    else{
                        $(".folder-name-back-button-container, .source-codes-container").css("left", "0");
                        $(".folder-name-back-button-container, .source-codes-container").css("width", "calc(100% - 1vw)");
                    }
                });

                $(".source-code-div").click(function(e){
                    if(!$(e.target).is(".source-code-buttons-div button, .source-code-buttons-div button i") && !$(e.target).is(".source-code-share-div, .source-code-delete-div, .source-code-info-div") && !$(e.target).is(".source-code-copy-button, .source-code-delete-button-confirm, .source-code-delete-button-confirm i") && !$(e.target).is(".source-code-info-div div, .source-code-info-div div strong, .source-code-info-div div small") && !$(e.target).is(".source-code-buttons-div-mob, .source-code-buttons-div-mob button, .source-code-buttons-div-mob button i") && !$(e.target).is(".source-code-download-button a")){
                        var id = $(this).children(".source-code-main-div").children(".source-code-share-div").children(".source-code-share-link").attr("id");

                        var url = window.location.href;

                        if(url.indexOf("&iid") > -1){
                            url = url.substring(0, url.indexOf("&iid"));
                            window.location.href = url + "&iid=" + id + "&t=s";
                        }
                        else{
                            window.location.href += "&iid=" + id + "&t=s";
                        }
                    }
                });

                $(".folder-div").click(function(e){
                    if(!$(e.target).is(".folder-menu-button, .folder-menu-button i")){
                        var id = $(this).children("div").attr("id");

                        var url = window.location.href;

                        if(url.indexOf("&iid") > -1){
                            url = url.substring(0, url.indexOf("&iid"));
                            window.location.href = url + "&iid=" + id + "&t=f";
                        }
                        else{
                            window.location.href += "&iid=" + id + "&t=f";
                        }
                    }
                });

                $(".source-code-view-button").click(function(){
                    $(this).parent().prev().click();
                });

                $(".folder-view-button").click(function(){
                    $(this).parent().prev().click();
                });

                $(".folder-info-button-menu-mob").click(function(){
                    $(this).parent().prev().children(".folder-info-div").clone().appendTo(".modal");
                    $(".modal").css("display", "flex");
                    $(".modal .folder-info-div").css("display", "block");
                    $(".modal-content").css("display", "none");

                    if($(window).width() <= 1200){
                        $("#header").css("display", "none");
                    }
                });

                $(".source-code-info-button").click(function(){
                    $(this).parent().prev().children(".source-code-main-div").children(".source-code-info-div").clone().appendTo(".modal");
                    $(".modal").css("display", "flex");
                    $(".modal .source-code-info-div").css("display", "block");
                    $(".modal-content").css("display", "none");
                    if($(window).width() <= 1200){
                        $("#header").css("display", "none");
                    }
                });

                $(".folder-share-button").click(function(){
                    var temp = $("<input></input>");
                    $("#body").append(temp);
                    temp.val($(this).attr("share-link")).select();
                    document.execCommand("copy");
                    temp.remove();
                    $("#success-msg p").html("<i class='fas fa-link'></i> <?php echo getTranslatedContent("account_message_link_copied_to_clipboard"); ?>");
                    $("#success-msg").css("display", "block");
                    setTimeout(function(){
                        $("#success-msg").css("display", "none")
                    }, 2500);
                });

                $(".source-code-share-button").click(function(){
                    var temp = $("<input></input>");
                    $("#body").append(temp);
                    temp.val($(this).parent().prev().children(".source-code-main-div").children(".source-code-share-div").children("a").html()).select();
                    document.execCommand("copy");
                    temp.remove();
                    $("#success-msg p").html("<i class='fas fa-link'></i> <?php echo getTranslatedContent("account_message_link_copied_to_clipboard"); ?>");
                    $("#success-msg").css("display", "block");
                    setTimeout(function(){
                        $("#success-msg").css("display", "none")
                    }, 2500);
                });

                $("#source-code-view-button-cm").click(function(){
                    $("#" + contextMenuClickedItem).click();
                });

                $("#source-code-download-button-cm").click(function(){
                    if($("#" + contextMenuClickedItem).parent().attr("class") != "folder-div"){
                        $("#" + contextMenuClickedItem).children(".source-code-main-div").children(".source-code-buttons-div").children(".source-code-download-button").children("a")[0].click();
                    }
                    else{
                        $("#" + contextMenuClickedItem).parent().next(".folder-div-mob-buttons").children(".folder-download-button").children("a")[0].click();
                    }
                });

                $("#source-code-share-button-cm").click(function(){
                    if($("#" + contextMenuClickedItem).parent().attr("class") != "folder-div"){
                        $("#" + contextMenuClickedItem).children(".source-code-main-div").children(".source-code-share-div").children(".source-code-copy-button").click();
                        $("#success-msg p").html("<i class='fas fa-link'></i> <?php echo getTranslatedContent("account_message_link_copied_to_clipboard"); ?>");
                        $("#success-msg").css("display", "block");
                        setTimeout(function(){
                            $("#success-msg").css("display", "none")
                        }, 2500);
                    }
                    else{
                        var temp = $("<input></input>");
                        $("#body").append(temp);
                        temp.val($("#" + contextMenuClickedItem).parent().next(".folder-div-mob-buttons").children(".folder-share-button").attr("share-link")).select();
                        document.execCommand("copy");
                        temp.remove();
                        $("#success-msg p").html("<i class='fas fa-link'></i> <?php echo getTranslatedContent("account_message_link_copied_to_clipboard"); ?>");
                        $("#success-msg").css("display", "block");
                        setTimeout(function(){
                            $("#success-msg").css("display", "none")
                        }, 2500);
                    }
                });

                $(".source-code-copy-button").click(function(){
                    var temp = $("<input></input>");
                    $("#body").append(temp);
                    temp.val($(this).parent().children(".source-code-share-link").html()).select();
                    document.execCommand("copy");
                    temp.remove();
                });

                $("#source-code-info-button-cm").click(function(){
                    if($("#" + contextMenuClickedItem).parent().attr("class") != "folder-div"){
                        $("#" + contextMenuClickedItem).children(".source-code-main-div").children(".source-code-info-div").clone().appendTo(".modal");
                        $(".modal").css("display", "flex");
                        $(".modal .source-code-info-div").css("display", "block");
                        $(".modal-content").css("display", "none");
                        if($(window).width() <= 1200){
                            $("#header").css("display", "none");
                        }
                    }
                    else{
                        $("#" + contextMenuClickedItem).parent().children(".folder-info-div").clone().appendTo(".modal");
                        $(".modal").css("display", "flex");
                        $(".modal .folder-info-div").css("display", "block");
                        $(".modal-content").css("display", "none");
                        if($(window).width() <= 1200){
                            $("#header").css("display", "none");
                        }
                    }
                });

                $(".folder-div").mouseenter(function(){
                    $(this).children().children(".folder-main-div").css("color", "var(--text-color)");
                    $(this).children().children(".folder-main-div").css("background-color", "var(--header-color)");
                });

                $(".folder-div").mouseleave(function(){
                    $(this).children().children(".folder-main-div").css("color", "var(--header-color)");
                    $(this).children().children(".folder-main-div").css("background-color", "var(--text-color)");
                });

                $(".source-code-div").mouseenter(function(){
                    $(this).children(".source-code-main-div").css("color", "var(--header-color)");
                    $(this).children(".source-code-main-div").css("background-color", "var(--text-color)");
                    $(this).children(".source-code-main-div").children(".source-code-buttons-div").children().css("color", "var(--text-color)");
                    $(this).children(".source-code-main-div").children(".source-code-buttons-div").children().css("background-color", "var(--header-color)");
                    $(this).children(".source-code-main-div").children(".source-code-buttons-div").children().css("border", "2px solid var(--header-color)");
                    $(this).children(".source-code-main-div").children(".source-code-share-div, .source-code-delete-div, .source-code-info-div").css("color", "var(--text-color)");
                    $(this).children(".source-code-main-div").children(".source-code-share-div, .source-code-delete-div, .source-code-info-div").css("background-color", "var(--header-color)");
                    $(this).children(".source-code-main-div").children(".source-code-share-div").children("a").css("color", "var(--text-color)");
                });

                $(".source-code-div").mouseleave(function(){
                    $(this).children(".source-code-main-div").css("color", "var(--text-color)");
                    $(this).children(".source-code-main-div").css("background-color", "var(--header-color)");
                    $(this).children(".source-code-main-div").children(".source-code-buttons-div").children().css("color", "var(--header-color)");
                    $(this).children(".source-code-main-div").children(".source-code-buttons-div").children().css("background-color", "var(--text-color)");
                    $(this).children(".source-code-main-div").children(".source-code-buttons-div").children().css("border", "2px solid var(--text-color)");
                    $(this).children(".source-code-main-div").children(".source-code-share-div, .source-code-delete-div, .source-code-info-div").css("color", "var(--header-color)");
                    $(this).children(".source-code-main-div").children(".source-code-share-div, .source-code-delete-div, .source-code-info-div").css("background-color", "var(--text-color)");
                    $(this).children(".source-code-main-div").children(".source-code-share-div").children("a").css("color", "var(--header-color)");
                });

                $(".folder-menu-button").click(function(e){
                    $(".account-context-menu").css("height", "unset");

                    contextMenuClickedItem = $(e.target).parentsUntil("#source-codes-container")[$(e.target).parentsUntil("#source-codes-container").length - 1];
                    contextMenuClickedItem = $(contextMenuClickedItem).attr("id");

                    try{
                        if($("#" + contextMenuClickedItem).children("div:first-child").attr("id").split("-")[0] == "f"){
                            contextMenuClickedItem = $(e.target).parentsUntil("#source-codes-container")[$(e.target).parentsUntil("#source-codes-container").length - 2];
                            contextMenuClickedItem = $(contextMenuClickedItem).children().attr("id");
                        }
                    }
                    catch(err){
                        //error catched
                    }
                        
                    $(".account-context-menu").css("display", "block");
                    if(e.pageY + $(".account-context-menu").outerHeight(true) <= $(window).outerHeight(true) + $(window).scrollTop() && e.pageX + $(".account-context-menu").outerWidth(true) <= $(window).outerWidth(true)){
                        $(".account-context-menu").css("top", e.pageY);
                        $(".account-context-menu").css("left", e.pageX);
                    }
                    else if(e.pageY + $(".account-context-menu").outerHeight(true) > $(window).outerHeight(true) + $(window).scrollTop() && e.pageX + $(".account-context-menu").outerWidth(true) > $(window).outerWidth(true)) {
                        $(".account-context-menu").css("top", e.pageY - $(".account-context-menu").outerHeight(true));
                        $(".account-context-menu").css("left", e.pageX - $(".account-context-menu").outerWidth(true));
                    }
                    else if(e.pageY + $(".account-context-menu").outerHeight(true) > $(window).outerHeight(true) + $(window).scrollTop()){
                        $(".account-context-menu").css("top", e.pageY - $(".account-context-menu").outerHeight(true));
                        $(".account-context-menu").css("left", e.pageX);
                    }
                    else if(e.pageX + $(".account-context-menu").outerWidth(true) > $(window).outerWidth(true)){
                        $(".account-context-menu").css("top", e.pageY);
                        $(".account-context-menu").css("left", e.pageX - $(".account-context-menu").outerWidth(true));
                    }

                    var contextMenuTop = $(".account-context-menu").css("top");
                    contextMenuTop = parseInt(contextMenuTop.substr(0, contextMenuTop.indexOf("p")));

                    if(contextMenuTop < $("#header").outerHeight(true)){
                        if(contextMenuTop < 0){
                            contextMenuTop *= -1;
                        }

                        $(".account-context-menu").css("height", $(".account-context-menu").outerHeight(true) - contextMenuTop - $("#header").outerHeight(true));
                        $(".account-context-menu").css("top", "calc(" + $(".account-context-menu").css("top") + " + " + (contextMenuTop + $("#header").outerHeight(true) + "px") + ")");
                    }
                });

                $(".source-code-buttons-div-mob button").click(function(e){
                    $(".account-context-menu").css("height", "unset");

                    contextMenuClickedItem = $(e.target).parentsUntil("#source-codes-container")[$(e.target).parentsUntil("#source-codes-container").length - 1];
                    contextMenuClickedItem = $(contextMenuClickedItem).attr("id");

                    try{
                        if($("#" + contextMenuClickedItem).children("div:first-child").attr("id").split("-")[0] == "f"){
                            contextMenuClickedItem = $(e.target).parentsUntil("#source-codes-container")[$(e.target).parentsUntil("#source-codes-container").length - 2];
                            contextMenuClickedItem = $(contextMenuClickedItem).children().attr("id");
                        }
                    }
                    catch(err){
                        //error catched
                    }
                        
                    $(".account-context-menu").css("display", "block");
                    if(e.pageY + $(".account-context-menu").outerHeight(true) <= $(window).outerHeight(true) + $(window).scrollTop() && e.pageX + $(".account-context-menu").outerWidth(true) <= $(window).outerWidth(true)){
                        $(".account-context-menu").css("top", e.pageY);
                        $(".account-context-menu").css("left", e.pageX);
                    }
                    else if(e.pageY + $(".account-context-menu").outerHeight(true) > $(window).outerHeight(true) + $(window).scrollTop() && e.pageX + $(".account-context-menu").outerWidth(true) > $(window).outerWidth(true)) {
                        $(".account-context-menu").css("top", e.pageY - $(".account-context-menu").outerHeight(true));
                        $(".account-context-menu").css("left", e.pageX - $(".account-context-menu").outerWidth(true));
                    }
                    else if(e.pageY + $(".account-context-menu").outerHeight(true) > $(window).outerHeight(true) + $(window).scrollTop()){
                        $(".account-context-menu").css("top", e.pageY - $(".account-context-menu").outerHeight(true));
                        $(".account-context-menu").css("left", e.pageX);
                    }
                    else if(e.pageX + $(".account-context-menu").outerWidth(true) > $(window).outerWidth(true)){
                        $(".account-context-menu").css("top", e.pageY);
                        $(".account-context-menu").css("left", e.pageX - $(".account-context-menu").outerWidth(true));
                    }

                    var contextMenuTop = $(".account-context-menu").css("top");
                    contextMenuTop = parseInt(contextMenuTop.substr(0, contextMenuTop.indexOf("p")));

                    if(contextMenuTop < $("#header").outerHeight(true)){
                        if(contextMenuTop < 0){
                            contextMenuTop *= -1;
                        }

                        $(".account-context-menu").css("height", $(".account-context-menu").outerHeight(true) - contextMenuTop - $("#header").outerHeight(true));
                        $(".account-context-menu").css("top", "calc(" + $(".account-context-menu").css("top") + " + " + (contextMenuTop + $("#header").outerHeight(true) + "px") + ")");
                    }
                });

                $("#sort-by").change(function(){
                    var order = $("#sort-by")[0][$("#sort-by")[0].selectedIndex].value;

                    var url = window.location.href;
                    var param = url.split("?");

                    if(param.length > 1){
                        if(url.split("&").length > 1){
                            if(!url.includes("order=")){
                                window.location.href = url + "&order=" + order;
                            }
                            else{
                                window.location.href = url.substr(0, url.lastIndexOf("order=")) + "order=" + order;
                            }
                        }
                        else{
                            if(url.split("order=").length > 1){
                                window.location.href = url.substr(0, url.lastIndexOf("order=")) + "order=" + order;
                            }
                            else{
                                window.location.href = url + "&order=" + order;
                            }
                        }
                    }
                    else{
                        window.location.href = url + "?order=" + order;
                    }
                });

                $(".order-change-button").click(function(){
                    var url = window.location.href;
                    var param = url.split("&");

                    if(url.includes("&d=")){
                        var d = url.split("&d=")[1];

                        if(d == "asc"){
                            d = "desc"
                        }
                        else{
                            d = "asc"
                        }

                        window.location.href = url.substr(0, url.lastIndexOf("&d=")) + "&d=" + d;
                    }
                    else{
                        var d = "desc";

                        window.location.href = url + "&d=" + d;
                    }
                });

                $("#folder-back-button").click(function(){
                    <?php
                        $sqlQuery = "SELECT * FROM folders WHERE folderID=?";
                        $stmt = mysqli_stmt_init($conn);

                        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                            echo '';

                            exit();
                        }

                        mysqli_stmt_bind_param($stmt, "s", $_GET['iid']);
                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt);
                        $folder = mysqli_fetch_assoc($result);

                        $sqlQuery = "SELECT * FROM folders WHERE pathToThis=?";
                        $stmt = mysqli_stmt_init($conn);

                        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                            echo '';

                            exit();
                        }

                        mysqli_stmt_bind_param($stmt, "s", $folder['folder']);
                        mysqli_stmt_execute($stmt);
                            
                        $result = mysqli_stmt_get_result($stmt);
                        $folder = mysqli_fetch_assoc($result);

                        if($folder['folderID'] != $_GET['fid']){
                            echo 'window.location.href = "./?fid='.$_GET['fid']."&iid="; echo $folder['folderID']; echo'&t=f"';
                        }
                        else{
                            echo 'window.location.href = "./?fid='.$_GET['fid']; echo'"';
                        }
                    ?>
                });

                $("#download-shared-folder-button").click(function(){
                    $(this).next("#shared-folder-download-link")[0].click();
                });

                $("#search-box").on("keyup input", function(){
                    fileFolderSearch($(this).val());
                });

                $("#search-box-type").change(function(){
                    var suffixSearchPlaceholder = [
                        "", //name
                        "(DD/MM/YYYY)", //date
                        "", //size
                        "", //language / type
                        "(DD/MM/YYYY)", //last modified
                    ][$("#search-box-type")[0].selectedIndex];

                    $("#search-box").attr("placeholder", $("#search-box").attr("placeholder").substr(0, $("#search-box").attr("placeholder").indexOf("...") + 3) + " " + suffixSearchPlaceholder);
                });

                function fileFolderSearch(input){
                    noElementFound = true;

                    var elementToFind = [
                        "small[sort-data=name-info]", //by name
                        "small[sort-data=date-info]", //by date
                        "small[sort-data=size-info]", //by size
                        "small[sort-data=lang-type-info]", //by language / type
                        "small[sort-data=last-mod-info]", //by last modified
                    ][$("#search-box-type")[0].selectedIndex];

                    if(elementToFind == "small[sort-data=date-info]" || elementToFind == "small[sort-data=last-mod-info]"){
                        if(input.substr(0, 1) == 0){
                            input = input.substr(1);
                        }
                    }

                    var elements = ".source-code-div";

                    if(elementToFind != "small[sort-data=lang-type-info]"){
                        elements = ".folder-div, " + elements;
                    }
                    else{ //hide folders if the user is searching by language
                        $(".folder-div").each(function(i, obj){
                            $(this).css("display", "none");
                            $(this).addClass("hidden");
                        });

                        correctMarginsOnSearch();
                    }

                    $(elements).each(function(i, obj){
                        if($(this).find(elementToFind).html().toLowerCase().indexOf(input) == -1){
                            $(this).css("display", "none");
                            $(this).addClass("hidden");
                        }
                        else{
                            $(this).css("display", "flex");
                            $(this).removeClass("hidden");
                            noElementFound = false;
                        }

                        correctMarginsOnSearch();
                    });

                    if(noElementFound && $("#empty-folder-div").css("display") == "none"){
                        $("#no-elements-found-on-search-div").css("display", "flex");
                    }
                    else{
                        $("#no-elements-found-on-search-div").css("display", "none");
                    }
                }

                function correctMarginsOnSearch(){
                    $(".folder-div:not(.hidden)").each(function(i, obj){
                        if((i % 3 == 0) || i == 0){
                            $(this).css("margin-left", "0");
                        }
                        else{
                            $(this).css("margin-left", "5%");
                        }
                    });

                    $(".source-code-div:not(.hidden)").each(function(i, obj){
                        if((i % 3 == 0) || i == 0){
                            $(this).css("margin-left", "0");
                        }
                        else{
                            $(this).css("margin-left", "5%");
                        }
                    });
                }
            });

            $("#account-div-for-cm").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 1vw)");
            $("html").css("height", "calc(100% - " + $("#header").outerHeight(true) + "px - 1vw)");

            $(window).on("load", function(){
                $("#account-div-for-cm").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 1vw)");
                $("html").css("height", "calc(100% - " + $("#header").outerHeight(true) + "px - 1vw)");
            });

            $(window).resize(function(){
                $("#account-div-for-cm").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 1vw)");
                $("html").css("height", "calc(100% - " + $("#header").outerHeight(true) + "px - 1vw)");
            });

            $(document).on("contextmenu", function(e){
                $(".account-context-menu").css("height", "unset");

                e.preventDefault();

                contextMenuClickedItem = $(e.target).parentsUntil("#source-codes-container")[$(e.target).parentsUntil("#source-codes-container").length - 1];
                contextMenuClickedItem = $(contextMenuClickedItem).attr("id");

                try{
                    if($("#" + contextMenuClickedItem).children("div:first-child").attr("id").split("-")[0] == "f"){
                        contextMenuClickedItem = $(e.target).parentsUntil("#source-codes-container")[$(e.target).parentsUntil("#source-codes-container").length - 2];
                        contextMenuClickedItem = $(contextMenuClickedItem).children().attr("id");
                    }
                }
                catch(err){
                    //error catched
                }

                if($.contains(document.getElementById("account-div-for-cm"), e.target)){
                    if($.contains(document.getElementById("source-codes-container"), e.target) && e.target.id != "folder-container" && $("#" + contextMenuClickedItem).attr("id") != undefined && e.target.id != "empty-folder-div-h3" && e.target.id != "empty-folder-div-img" && e.target.id != "no-elements-found-on-search-div-h3" && e.target.id != "no-elements-found-on-search-div-img"){
                        $(".account-context-menu hr").css("display", "block");
                        $(".file-related-cm-option").css("display", "block");
                        $(".account-context-menu").css("display", "block");
                    }
                    else{
                        $(".account-context-menu").css("display", "none");
                    }

                    if(e.pageY + $(".account-context-menu").outerHeight(true) <= $(window).outerHeight(true) + $(window).scrollTop() && e.pageX + $(".account-context-menu").outerWidth(true) <= $(window).outerWidth(true)){
                        $(".account-context-menu").css("top", e.pageY);
                        $(".account-context-menu").css("left", e.pageX);
                    }
                    else if(e.pageY + $(".account-context-menu").outerHeight(true) > $(window).outerHeight(true) + $(window).scrollTop() && e.pageX + $(".account-context-menu").outerWidth(true) > $(window).outerWidth(true)) {
                        $(".account-context-menu").css("top", e.pageY - $(".account-context-menu").outerHeight(true));
                        $(".account-context-menu").css("left", e.pageX - $(".account-context-menu").outerWidth(true));
                    }
                    else if(e.pageY + $(".account-context-menu").outerHeight(true) > $(window).outerHeight(true) + $(window).scrollTop()){
                        $(".account-context-menu").css("top", e.pageY - $(".account-context-menu").outerHeight(true));
                        $(".account-context-menu").css("left", e.pageX);
                    }
                    else if(e.pageX + $(".account-context-menu").outerWidth(true) > $(window).outerWidth(true)){
                        $(".account-context-menu").css("top", e.pageY);
                        $(".account-context-menu").css("left", e.pageX - $(".account-context-menu").outerWidth(true));
                    }

                    var contextMenuTop = $(".account-context-menu").css("top");
                    contextMenuTop = parseInt(contextMenuTop.substr(0, contextMenuTop.indexOf("p")));

                    if(contextMenuTop < $("#header").outerHeight(true)){
                        if(contextMenuTop < 0){
                            contextMenuTop *= -1;
                        }

                        $(".account-context-menu").css("height", $(".account-context-menu").outerHeight(true) - contextMenuTop - $("#header").outerHeight(true));
                        $(".account-context-menu").css("top", "calc(" + $(".account-context-menu").css("top") + " + " + (contextMenuTop + $("#header").outerHeight(true) + "px") + ")");
                    }
                }
                else{
                    $(".account-context-menu").css("display", "none");
                }

                return false;
            });

            $(document).scroll(function(){
                $(".account-context-menu").css("display", "none");
            });

            $(document).click(function(e){
                if(!$(e.target).is(".folder-menu-button, .folder-menu-button i, .source-code-buttons-div-mob button, .source-code-buttons-div-mob button i")){
                    $(".account-context-menu").css("display", "none");
                }
            });

            $(window).resize(function(){
                $(".account-context-menu").css("display", "none");
            });
        </script>
<?php
    }
?>