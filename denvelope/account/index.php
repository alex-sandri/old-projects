<?php
    session_start();

    if(isset($_GET['order'])){
        $order = $_GET['order'];

        if($order != "name" && $order != "date" && $order != "size" && $order != "language" && $order != "last-modified"){
            header("Location: ./");
            exit();
        }
    }

    if(isset($_GET['d']) && $_GET['d'] != "asc" && $_GET['d'] != "desc"){
        header("Location: ./");
        exit();
    }

    require("../php/dbh.php");
    require("../php/get-current-user.php");
    require("../php/global-vars.php");
    require_once("../php/create-cookie.php");

    if(isset($_COOKIE['userSession'])){
        $sessionID = $_COOKIE['userSession'];

        if(!ctype_xdigit($sessionID)){
            $cookieError = "notValidCookie";

            require("../php/delete-cookie.php");
            deleteCookie("userSession");

            header("Location: ../");
            exit();
        }

        $sqlQuery = "SELECT * FROM sessions WHERE sessionID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $sqlError = "sqlError";

            header("Location: ../");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $sessionID);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if($row){
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];

                $sessionID = bin2hex(random_bytes(64));
                            
                $sqlQuery = "UPDATE sessions SET sessionID=? WHERE (username=? OR email=?) AND sessionID=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $sqlError = "sqlError";

                    header("Location: ../");
                    exit();
                }
                else{
                    mysqli_stmt_bind_param($stmt, "ssss", $sessionID, $_SESSION['username'], $_SESSION['email'], $_COOKIE['userSession']);
                    mysqli_stmt_execute($stmt);
                                
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];

                    createCookie("userSession", $sessionID);

                    mysqli_stmt_close($stmt);
                }
            }
            else{
                header("Location: ../php/logout.php?ref=account");
                exit();
            }
        }
    }

    if(!isset($_SESSION['username'])){
        header("Location: ../login/?ref=account");
        exit();
    }

    $user = getUser();

    $_SESSION['plan'] = $user['plan'];
?>

<?php
    $enablePayments = false;
    $betaDisableAccess = false;
    $betaDisableAccessHeaderAccount = false;
?>

<?php
    if(isset($_COOKIE['userSession'])){
        require("../php/update-last-activity.php");

        updateLastActivity($sessionID);
    }
?>

<?php
    require("../lang/".$lang.".php");
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <?php
        if($isProduction){
            echo $googleAnalyticsTag;
        }

        if($isProduction){
            echo $hotjarTag;
        }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="<?php echo $HEADER_COLOR; ?>">
    <meta name="msapplication-navbutton-color" content="<?php echo $HEADER_COLOR; ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>
        <?php
            $folderName = "";

            if(isset($_GET['folder'])){
                    
                require("../php/dbh.php");

                $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    exit();
                }

                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['folder']);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                $folder = mysqli_fetch_assoc($result);

                echo htmlspecialchars($folder['name'])." - Denvelope";
                $folderName = htmlspecialchars($folder['name']);
            }
            else if(isset($_GET['view'])){
                require("../php/dbh.php");

                $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND fileID=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    exit();
                }

                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['view']);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                $file = mysqli_fetch_assoc($result);

                echo htmlspecialchars($file['name'])." - Denvelope";
            }
            else{
                echo getTranslatedContent("account_title").' - Denvelope';
            }
        ?>
    </title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <!--
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/icons.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    -->
    <link rel="stylesheet" href="../css/main.min.css">
    <script rel="dns-prefetch" src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/devicon/2.2/devicon.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/add-source-code.php"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/pace.js"></script>
    <script src="../js/ace/src-min-noconflict/ace.js"></script>
    <script src="../js/ace/src-min-noconflict/ext-language_tools.js"></script>
</head>
<body id="body">

    <div class="file-save-success-msg" id="success-msg">
        <p></p>
    </div>
    <div class="file-save-success-msg" id="error-msg">
        <p></p>
    </div>

    <?php
        if(!isset($_GET['view'])){
            require("../php/header.php");
        }
    ?>

    <div id="account-div-for-cm" style="height: 100%;">
        <div class="main-div-account" id="main-div-account">
            <?php
                if(!isset($_GET['view'])){
                    echo '<div id="top-div-account">
                            <div>
                                <button onclick="addSourceCode()" class="add-source-code-button" id="add-source-code-button"><i class="fas fa-plus"></i> '; echo getTranslatedContent("account_add_source_code"); echo'</button>
                                <button class="add-source-code-button" id="new-file-button"><i class="fas fa-file-code"></i> '; echo getTranslatedContent("account_create_new_file"); echo'</button>
                                <button class="add-source-code-button" id="new-folder-button"><i class="fas fa-folder-plus"></i> '; echo getTranslatedContent("account_create_new_folder"); echo'</button>
                            </div>';
                            echo '<div class="upgrade-space-div">';
                            if($_SESSION['plan'] == "Free" && $enablePayments){
                                echo '
                                    <a href="settings/#plan" class="account-page-upgrade"><i class="fas fa-level-up-alt"></i> '; echo getTranslatedContent("account_upgrade_plan"); echo'</a>
                                ';
                            }
                            echo'<div class="available-storage-space">
                                <small style="text-transform: uppercase;">'; echo getTranslatedContent("account_used_storage_space"); echo':</small><br>
                                <h3>
                                    ';
                                        require("../php/dbh.php");
            
                                        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
                                        $stmt = mysqli_stmt_init($conn);
            
                                        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                            echo '';
            
                                            exit();
                                        }
            
                                        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                                        mysqli_stmt_execute($stmt);
            
                                        $result = mysqli_stmt_get_result($stmt);
                                        $user = mysqli_fetch_assoc($result);
            
                                        echo "<span id='used-storage-span'>".$user['usedStorage']."</span>";
                                        echo " / ";
                                        echo $user['maxStorage'];                                   
                                    echo '
                                </h3>
                            </div>';
                            echo '</div>';
                        ?>
                        <div class="search-container">
                            <select name="search-box-type" id="search-box-type" class="search-box-type">
                                <option value="name" selected><?php echo getTranslatedContent("account_search_by_name"); ?></option>
                                <option value="date"><?php echo getTranslatedContent("account_search_by_date"); ?></option>
                                <option value="size"><?php echo getTranslatedContent("account_search_by_size"); ?></option>
                                <option value="language"><?php echo getTranslatedContent("account_search_by_language"); ?></option>
                                <option value="last-modified"><?php echo getTranslatedContent("account_search_by_last_modified"); ?></option>
                            </select>
                            <input type="search" class="input search-box" name="search-box" id="search-box" placeholder="<?php //echo "&#xf002; "; ?><?php echo getTranslatedContent("account_search_box"); ?>..." autocorrect="off" autocapitalize="off" spellcheck="false">
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
                                <select name="sort-by" id="sort-by" class="sort-by <?php echo isset($_GET['order']) ? "order-change-button-visible" : "" ?>">
                                    <option value="0" hidden <?php if(!isset($_GET['order'])) echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by"); ?></option>
                                    <option value="name" <?php if(isset($_GET['order']) && $_GET['order'] == "name") echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by_name"); ?></option>
                                    <option value="date" <?php if(isset($_GET['order']) && $_GET['order'] == "date") echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by_date"); ?></option>
                                    <option value="size" <?php if(isset($_GET['order']) && $_GET['order'] == "size") echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by_size"); ?></option>
                                    <option value="language" <?php if(isset($_GET['order']) && $_GET['order'] == "language") echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by_language"); ?></option>
                                    <option value="last-modified" <?php if(isset($_GET['order']) && $_GET['order'] == "last-modified") echo 'selected'; ?>><?php echo getTranslatedContent("account_sort_by_last_modified"); ?></option>
                                </select>
                            </div>
                        </div>
                        <?php
                        echo'</div>
                    ';
                }
            ?>
            <div class="modal" id="modal">
                <div class="modal-content" id="modal-select">
                    <div id="modal-select-no-mob-div">
                        <h2 class="modal-select-h2"><?php echo getTranslatedContent("account_choose_type"); ?></h2>
                        <div class="modal-select-buttons-div">
                            <button class="modal-select-button" onclick="singleFile()"><i class="fas fa-file"></i> <?php echo getTranslatedContent("account_single_file"); ?></button>
                            <button class="modal-select-button" onclick="project()"><i class="fas fa-folder"></i> <?php echo getTranslatedContent("account_project"); ?></button>
                            <button class="modal-select-button" onclick="multipleFiles()"><i class="fas fa-copy"></i> <?php echo getTranslatedContent("account_multiple_files"); ?></button>
                        </div>
                    </div>
                    <h2 class="modal-select-h2 mob-select"><?php echo getTranslatedContent("account_choose_type"); ?></h2>
                    <div class="modal-select-buttons-div mob-select">
                        <button class="modal-select-button mob-select" onclick="singleFile()"><i class="fas fa-file"></i> <?php echo getTranslatedContent("account_single_file"); ?></button>
                        <button class="modal-select-button mob-select" onclick="project()"><i class="fas fa-folder"></i> <?php echo getTranslatedContent("account_project"); ?></button>
                        <button class="modal-select-button mob-select" onclick="multipleFiles()"><i class="fas fa-copy"></i> <?php echo getTranslatedContent("account_multiple_files"); ?></button>
                    </div>
                    <div class="modal-select-cancel-div">
                        <button class="modal-select-button modal-select-cancel-button" onclick="cancelAddSourceCode()"><i class="fas fa-times"></i> <?php echo getTranslatedContent("account_cancel"); ?></button>
                    </div>
                </div>
                <div class="modal-content" id="modal-content" style="display: none;">
                    <form action="../php/add-source-code.php" method="post" enctype="multipart/form-data" class="add-source-code-form" id="single-file" style="display: none;">   
                        <h2 class="add-source-code-h2"><?php echo getTranslatedContent("account_single_file_title"); ?></h2>                                   
                        <div>
                            <div class="language-select-name-input-container">
                                <select name="language" id="language" class="language-select" placeholder="<?php echo getTranslatedContent("account_single_file_select_language"); ?>">
                                    <!--icon||syntaxHighlighter||languageName-->
                                    <option value="0" hidden selected><?php echo getTranslatedContent("account_single_file_select_language"); ?></option>
                                    <option value="c||c_cpp||C">C</option>
                                    <option value="cplusplus||c_cpp||C++">C++</option> 
                                    <option value="csharp||csharp||C#">C#</option>   
                                    <option value="css3||css||CSS">CSS</option>  
                                    <option value="go||golang||Go">Go</option>   
                                    <option value="html5||html||HTML">HTML</option>  
                                    <option value="java||java||Java">Java</option>  
                                    <option value="javascript||javascript||Javascript">Javascript</option>
                                    <option value="php||php||PHP">PHP</option>   
                                    <option value="python||python||Python">Python</option>  
                                    <option value="rust-svg||rust||Rust">Rust</option>
                                    <option value="mysql||sql||SQL">SQL</option>
                                    <option value="swift||swift||Swift">Swift</option>  
                                    <option value="other||plain_text||text_file"><?php echo getTranslatedContent("account_file_language_text_file"); ?></option>
                                    <option value="other||plain_text||other"><?php echo getTranslatedContent("account_file_language_other"); ?></option> 
                                    <option value="other||other_archive||other_archive"><?php echo getTranslatedContent("account_file_language_other_archive"); ?></option> 
                                    <option value="other||other_audio||other_audio"><?php echo getTranslatedContent("account_file_language_other_audio"); ?></option>
                                    <option value="other||other_document||other_document"><?php echo getTranslatedContent("account_file_language_other_document"); ?></option>  
                                    <option value="other||other_image||other_image"><?php echo getTranslatedContent("account_file_language_other_image"); ?></option> 
                                    <option value="other||other_video||other_video"><?php echo getTranslatedContent("account_file_language_other_video"); ?></option>       
                                </select>
                                <input type="text" name="source-name" id="source-name" class="input-source-name" placeholder="<?php echo getTranslatedContent("account_single_file_name"); ?>">
                            </div>
                            <p class="error-input-field" id="single-file-name-error"></p>
                        </div>
                        <input type="file" name="file-single" id="file-single" class="source-file-upload">
                        <div class="buttons-container-add-source">
                            <div id="add-save-file-div-single">
                                <label for="file-single" class="source-file-upload-label" id="source-file-upload-label"><i class="fas fa-file-upload"></i> <?php echo getTranslatedContent("account_single_file_add_file"); ?></label>
                                <button type="submit" class="add-source-code-save-button" name="add-source-code-save-button-single" id="add-source-code-save-button-single"><i class="fas fa-save"></i> <?php echo getTranslatedContent("account_save"); ?></button>
                            </div>
                            <div class="add-source-code-back-cancel-button-container">
                                <a onclick="back()" class="add-source-code-back-button"><i class="fas fa-arrow-left"></i> <?php echo getTranslatedContent("account_back"); ?></a>
                                <a onclick="cancelAddSourceCode()" class="add-source-code-cancel-button" name="add-source-code-cancel-button-single"><i class="fas fa-times"></i> <?php echo getTranslatedContent("account_cancel"); ?></a>
                            </div>
                        </div>
                        <?php
                            require("../php/dbh.php");

                            if(isset($_GET['folder'])){
                                $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
                            }
                            else{
                                $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
                            }

                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                echo '';

                                exit();
                            }

                            if(isset($_GET['folder'])){
                                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['folder']);
                            }
                            else{
                                mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                            }

                            mysqli_stmt_execute($stmt);

                            $result = mysqli_stmt_get_result($stmt);

                            if(isset($_GET['folder'])){
                                $folder = mysqli_fetch_assoc($result);
                            }
                            else{
                                $user = mysqli_fetch_assoc($result);
                            }

                            echo '<input type="hidden" name="path-to-this" value="'; if(isset($_GET['folder'])) echo $folder['pathToThis']; else echo "../u/".$user['userID']; echo'">';
                        ?>
                    </form>
                    <form action="../php/add-source-code.php" method="post" enctype="multipart/form-data" class="add-source-code-form" id="project" style="display: none;">
                        <h2 class="add-source-code-h2"><?php echo getTranslatedContent("account_project_title"); ?></h2>                    
                        <div>
                            <input type="text" name="source-name-project" id="source-name-project" class="input-source-name" placeholder="<?php echo getTranslatedContent("account_project_name"); ?>">
                            <p class="error-input-field" id="project-name-error"></p>
                        </div>
                        <input type="file" name="file-project[]" id="file-project" class="source-file-upload" webkitdirectory mozdirectory msdirectory odirectory directory>
                        <div class="buttons-container-add-source">
                            <div id="add-save-file-div-project">
                                <label for="file-project" class="source-file-upload-label" id="source-folder-upload-label"><i class="fas fa-folder"></i> <?php echo getTranslatedContent("account_project_add_folder"); ?></label>
                                <button type="submit" class="add-source-code-save-button" name="add-source-code-project-button" id="add-source-code-project-button"><i class="fas fa-save"></i> <?php echo getTranslatedContent("account_save"); ?></button>
                            </div>
                            <div class="add-source-code-back-cancel-button-container">
                                <a onclick="back()" class="add-source-code-back-button"><i class="fas fa-arrow-left"></i> <?php echo getTranslatedContent("account_back"); ?></a>
                                <a onclick="cancelAddSourceCode()" class="add-source-code-cancel-button" name="add-source-code-project-cancel-button"><i class="fas fa-times"></i> <?php echo getTranslatedContent("account_cancel"); ?></a>
                            </div>
                        </div>
                        <input type="hidden" name="path-array" id="path-array" value="">
                        <input type="hidden" name="lang-array" id="lang-array" value="">
                        <?php
                            require("../php/dbh.php");

                            if(isset($_GET['folder'])){
                                $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
                            }
                            else{
                                $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
                            }

                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                echo '';

                                exit();
                            }

                            if(isset($_GET['folder'])){
                                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['folder']);
                            }
                            else{
                                mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                            }

                            mysqli_stmt_execute($stmt);

                            $result = mysqli_stmt_get_result($stmt);

                            if(isset($_GET['folder'])){
                                $folder = mysqli_fetch_assoc($result);
                            }
                            else{
                                $user = mysqli_fetch_assoc($result);
                            }

                            echo '<input type="hidden" name="path-to-this" value="'; if(isset($_GET['folder'])) echo $folder['pathToThis']; else echo "../u/".$user['userID']; echo'">';
                        ?>
                    </form>
                    <form action="../php/add-source-code.php" method="post" enctype="multipart/form-data" class="add-source-code-form" id="multiple-files" style="display: none;">
                        <h2 class="add-source-code-h2"><?php echo getTranslatedContent("account_multiple_files_title"); ?></h2>            
                        <input type="file" name="file-multiple[]" id="file-multiple" class="source-file-upload" multiple>
                        <p class="error-input-field" id="multiple-files-error"></p>
                        <div class="buttons-container-add-source">
                            <div id="add-save-file-div-multiple">
                                <label for="file-multiple" class="source-file-upload-label" id="source-multiple-upload-label"><i class="fas fa-copy"></i> <?php echo getTranslatedContent("account_multiple_files_add_files"); ?></label>
                                <button type="submit" class="add-source-code-save-button" name="add-source-code-multiple-button" id="add-source-code-multiple-button"><i class="fas fa-save"></i> <?php echo getTranslatedContent("account_save"); ?></button>
                            </div>
                            <div class="add-source-code-back-cancel-button-container">
                                <a onclick="back()" class="add-source-code-back-button"><i class="fas fa-arrow-left"></i> <?php echo getTranslatedContent("account_back"); ?></a>
                                <a onclick="cancelAddSourceCode()" class="add-source-code-cancel-button" name="add-source-code-multiple-cancel-button"><i class="fas fa-times"></i> <?php echo getTranslatedContent("account_cancel"); ?></a>
                            </div>
                        </div>
                        <input type="hidden" name="lang-array-multiple" id="lang-array-multiple" value="">
                        <?php
                            require("../php/dbh.php");

                            if(isset($_GET['folder'])){
                                $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
                            }
                            else{
                                $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
                            }

                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                echo '';

                                exit();
                            }

                            if(isset($_GET['folder'])){
                                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['folder']);
                            }
                            else{
                                mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                            }

                            mysqli_stmt_execute($stmt);

                            $result = mysqli_stmt_get_result($stmt);

                            if(isset($_GET['folder'])){
                                $folder = mysqli_fetch_assoc($result);
                            }
                            else{
                                $user = mysqli_fetch_assoc($result);
                            }

                            echo '<input type="hidden" name="path-to-this" value="'; if(isset($_GET['folder'])) echo $folder['pathToThis']; else echo "../u/".$user['userID']; echo'">';
                        ?>
                    </form>
                    <form action="../php/add-source-code.php" method="post" enctype="multipart/form-data" class="add-source-code-form" id="new-file-form" style="display: none;">
                        <h2 class="add-source-code-h2"><?php echo getTranslatedContent("account_create_new_file_title"); ?></h2>                    
                        <br>                  
                        <div class="language-select-name-input-container">
                            <select name="language-new-file" id="language-new-file" class="language-select" placeholder="<?php echo getTranslatedContent("account_create_new_file_select_language"); ?>">
                                <!--icon||syntaxHighlighter||languageName-->
                                <option value="0" hidden selected><?php echo getTranslatedContent("account_create_new_file_select_language"); ?></option>
                                <option value="c||c_cpp||C">C</option>
                                <option value="cplusplus||c_cpp||C++">C++</option> 
                                <option value="csharp||csharp||C#">C#</option>   
                                <option value="css3||css||CSS">CSS</option>  
                                <option value="go||golang||Go">Go</option>   
                                <option value="html5||html||HTML">HTML</option>  
                                <option value="java||java||Java">Java</option>  
                                <option value="javascript||javascript||Javascript">Javascript</option>
                                <option value="php||php||PHP">PHP</option>   
                                <option value="python||python||Python">Python</option>  
                                <option value="rust-svg||rust||Rust">Rust</option>
                                <option value="mysql||sql||SQL">SQL</option>
                                <option value="swift||swift||Swift">Swift</option>      
                                <option value="other||plain_text||text_file"><?php echo getTranslatedContent("account_file_language_text_file"); ?></option>  
                                <option value="other||plain_text||other"><?php echo getTranslatedContent("account_file_language_other"); ?></option> 
                            </select>
                            <input type="text" name="source-name-new-file" id="source-name-new-file" class="input-source-name" placeholder="<?php echo getTranslatedContent("account_create_new_file_name"); ?>">
                        </div>
                        <p class="error-input-field" id="new-file-name-error"></p>
                        <br>
                        <div class="add-source-code-back-cancel-button-container" style="display: flex; width: 100%; justify-content: space-between;">
                            <button type="submit" class="add-source-code-save-button" name="add-source-code-new-file-button" id="add-source-code-new-file-button"><i class="fas fa-save"></i> <?php echo getTranslatedContent("account_save"); ?></button>
                            <a onclick="cancelAddSourceCode()" class="add-source-code-cancel-button" name="add-source-code-new-file-cancel-button"><i class="fas fa-times"></i> <?php echo getTranslatedContent("account_cancel"); ?></a>
                        </div>
                        <?php
                            require("../php/dbh.php");

                            if(isset($_GET['folder'])){
                                $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
                            }
                            else{
                                $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
                            }

                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                echo '';

                                exit();
                            }

                            if(isset($_GET['folder'])){
                                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['folder']);
                            }
                            else{
                                mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                            }

                            mysqli_stmt_execute($stmt);

                            $result = mysqli_stmt_get_result($stmt);

                            if(isset($_GET['folder'])){
                                $folder = mysqli_fetch_assoc($result);
                            }
                            else{
                                $user = mysqli_fetch_assoc($result);
                            }

                            echo '<input type="hidden" name="path-to-this" value="'; if(isset($_GET['folder'])) echo $folder['pathToThis']; else echo "../u/".$user['userID']; echo'">';
                        ?>
                    </form>
                    <form action="../php/add-source-code.php" method="post" enctype="multipart/form-data" class="add-source-code-form" id="new-folder-form" style="display: none;">
                        <h2 class="add-source-code-h2"><?php echo getTranslatedContent("account_create_new_folder_title"); ?></h2>                    
                        <br> 
                        <input type="text" name="source-name-new-folder" id="source-name-new-folder" class="input-source-name" placeholder="<?php echo getTranslatedContent("account_create_new_folder_name"); ?>" style="border-left: 2px solid var(--text-color); border-radius: 5px;">
                        <p class="error-input-field" id="new-folder-name-error"></p>
                        <br>
                        <div class="add-source-code-back-cancel-button-container" style="display: flex; width: 100%; justify-content: space-between;">
                            <button type="submit" class="add-source-code-save-button" name="add-source-code-new-folder-button" id="add-source-code-new-folder-button"><i class="fas fa-save"></i> <?php echo getTranslatedContent("account_save"); ?></button>
                            <a onclick="cancelAddSourceCode()" class="add-source-code-cancel-button" name="add-source-code-new-folder-cancel-button"><i class="fas fa-times"></i> <?php echo getTranslatedContent("account_cancel"); ?></a>
                        </div>
                        <?php
                            require("../php/dbh.php");

                            if(isset($_GET['folder'])){
                                $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
                            }
                            else{
                                $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
                            }

                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                echo '';

                                exit();
                            }

                            if(isset($_GET['folder'])){
                                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['folder']);
                            }
                            else{
                                mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                            }

                            mysqli_stmt_execute($stmt);

                            $result = mysqli_stmt_get_result($stmt);

                            if(isset($_GET['folder'])){
                                $folder = mysqli_fetch_assoc($result);
                            }
                            else{
                                $user = mysqli_fetch_assoc($result);
                            }

                            echo '<input type="hidden" name="path-to-this" value="'; if(isset($_GET['folder'])) echo $folder['pathToThis']; else echo "../u/".$user['userID']; echo'">';
                        ?>
                    </form>
                </div>
                <div class="modal-content" id="modal-view-source">
                    <?php
                        if(isset($_GET['view'])){
                            require("../php/dbh.php");
                            require("../php/get-file-content-s3.php");
                            require("../php/get-object-url-s3.php");

                            echo '<script>$("#modal").css("display", "flex");$("#modal-select").css("display", "none");$("#modal-view-source").css("display", "flex");$("#body").css("overflow", "hidden");if($(window).width() <= 1200)$("#header").css("display", "none");$("#modal-select").addClass("no-display");</script>';

                            $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND fileID=?";
                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                echo '';

                                exit();
                            }

                            mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['view']);
                            mysqli_stmt_execute($stmt);

                            $result = mysqli_stmt_get_result($stmt);
                            $file = mysqli_fetch_assoc($result);

                            if(!$file){
                                echo "<script>window.location.href = './';</script>";
                                exit();
                            }

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
                                        <h1 class="view-source-h1">'; echo htmlspecialchars($file['name']); echo'</h1>
                                        <button class="source-code-button" id="view-source-close-button"><i class="fas fa-times"></i> '; echo getTranslatedContent("account_view_source_close"); echo'</button>
                                    </div>
                                    <div class="view-source-code" id="view-source-code">';
                                        echo htmlspecialchars(getFileContent($file['pathToThis']));
                                    echo '</div>
                                    <div class="view-source-bottom">
                                        <div class="view-source-bottom-left">
                                            <button class="source-code-button" id="view-source-info-button"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_view_source_info"); echo'</button>
                                            <button class="source-code-button" id="view-source-share-button"><i class="fas fa-share-alt"></i> '; echo getTranslatedContent("account_view_source_share"); echo'</button>
                                            <button class="source-code-button" id="view-source-download-button"><a href="../download/?sid='; echo $file['fileID']; echo'" style="display: none;" download></a><i class="fas fa-download"></i> '; echo getTranslatedContent("account_view_source_download"); echo'</button>
                                            <button class="source-code-button" id="view-source-edit-button"><i class="fas fa-edit"></i> '; echo getTranslatedContent("account_view_source_edit"); echo'</button>
                                            <input type="hidden" id="'; echo "https://denvelope.com/share?sid=".$file['fileID']; echo'"></input>
                                        </div>
                                        <div class="view-source-bottom-right">
                                            <button class="source-code-button" id="view-source-delete-button"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_view_source_delete"); echo'</button>
                                        </div>
                                        <div class="view-source-bottom-menu-mob">
                                            <button class="source-code-button" id="view-source-bottom-menu-button" style="position: absolute; bottom: 0;"><i class="fas fa-ellipsis-h"></i></button>
                                            <div id="view-source-menu-mob" style="display: none;">
                                                <button class="source-code-button" id="view-source-info-button-mob"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_view_source_info"); echo'</button>
                                                <button class="source-code-button" id="view-source-share-button-mob"><i class="fas fa-share-alt"></i> '; echo getTranslatedContent("account_view_source_share"); echo'</button>
                                                <button class="source-code-button" id="view-source-download-button-mob"><a href="../download/?sid='; echo $file['fileID']; echo'" style="display: none;" download></a><i class="fas fa-download"></i> '; echo getTranslatedContent("account_view_source_download"); echo'</button>
                                                <button class="source-code-button" id="view-source-edit-button-mob"><i class="fas fa-edit"></i> '; echo getTranslatedContent("account_view_source_edit"); echo'</button>
                                                <button class="source-code-button" id="view-source-delete-button-mob"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_view_source_delete"); echo'</button>
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
                                            <strong>'; echo getTranslatedContent("account_view_source_info_last_modified"); echo': </strong><small id="last-modified-file">'; echo $file['lastModified']; echo'</small>
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
                                        <h1 class="view-source-h1">'; echo htmlspecialchars($file['name']); echo'</h1>
                                        <button class="source-code-button" id="view-source-close-button"><i class="fas fa-times"></i> '; echo getTranslatedContent("account_view_source_close"); echo'</button>
                                    </div>
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
                                            <button class="source-code-button" id="view-source-download-button"><a href="../download/?sid='; echo $file['fileID']; echo'" style="display: none;" download></a><i class="fas fa-download"></i> '; echo getTranslatedContent("account_view_source_download"); echo'</button>
                                            <input type="hidden" id="'; echo "https://denvelope.com/share?sid=".$file['fileID']; echo'"></input>';
                                            if($fileLang[1] != "other_archive" && $fileLang[1] != "other_audio" && $fileLang[1] != "other_document" && $fileLang[1] != "other_image" && $fileLang[1] != "other_video"){
                                                echo '
                                                    <button class="source-code-button" id="view-source-edit-button"><i class="fas fa-edit"></i> '; echo getTranslatedContent("account_view_source_edit"); echo'</button>
                                                ';
                                            }
                                            echo '
                                        </div>
                                        <div class="view-source-bottom-right">
                                            <button class="source-code-button" id="view-source-delete-button"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_view_source_delete"); echo'</button>
                                        </div>
                                    </div>
                                    <div class="view-source-bottom-menu-mob" id="bottom-menu-mob-no-source">
                                        <button class="source-code-button" id="view-source-bottom-menu-button" style="position: absolute; bottom: 0;"><i class="fas fa-ellipsis-h"></i></button>
                                        <div id="view-source-menu-mob" style="display: none;">
                                            <button class="source-code-button" id="view-source-info-button-mob"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_view_source_info"); echo'</button>
                                            <button class="source-code-button" id="view-source-share-button-mob"><i class="fas fa-share-alt"></i> '; echo getTranslatedContent("account_view_source_share"); echo'</button>
                                            <button class="source-code-button" id="view-source-download-button-mob"><a href="../download/?sid='; echo $file['fileID']; echo'" style="display: none;" download></a><i class="fas fa-download"></i> '; echo getTranslatedContent("account_view_source_download"); echo'</button>
                                            <button class="source-code-button" id="view-source-delete-button-mob"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_view_source_delete"); echo'</button>
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
                        }
                    ?>
                </div>
                <div id="source-code-rename-div" class="modal-content" style="display: none;">
                    <h2 class="add-source-code-h2"><?php echo getTranslatedContent("account_rename_file"); ?></h2>
                    <br> 
                    <input type="text" name="rename-file" id="rename-file" class="input-source-name" placeholder="<?php echo getTranslatedContent("account_rename_file_name"); ?>" style="border-left: 2px solid var(--text-color); border-radius: 5px;">
                    <p class="error-input-field" id="rename-file-error"></p>
                    <br>
                    <div class="add-source-code-back-cancel-button-container" style="display: flex; width: 100%; justify-content: space-between;">
                        <button class="add-source-code-save-button" name="rename-file-button" id="rename-file-button"><i class="fas fa-save"></i> <?php echo getTranslatedContent("account_save"); ?></button>
                        <a onclick="cancelAddSourceCode()" class="add-source-code-cancel-button" name="rename-file-cancel-button"><i class="fas fa-times"></i> <?php echo getTranslatedContent("account_cancel"); ?></a>
                    </div>
                </div>
                <div id="folder-rename-div" class="modal-content" style="display: none;">
                <h2 class="add-source-code-h2"><?php echo getTranslatedContent("account_rename_folder"); ?></h2>
                    <br> 
                    <input type="text" name="rename-folder" id="rename-folder" class="input-source-name" placeholder="<?php echo getTranslatedContent("account_rename_folder_name"); ?>" style="border-left: 2px solid var(--text-color); border-radius: 5px;">
                    <p class="error-input-field" id="rename-folder-error"></p>
                    <br>
                    <div class="add-source-code-back-cancel-button-container" style="display: flex; width: 100%; justify-content: space-between;">
                        <button class="add-source-code-save-button" name="rename-folder-button" id="rename-folder-button"><i class="fas fa-save"></i> <?php echo getTranslatedContent("account_save"); ?></button>
                        <a onclick="cancelAddSourceCode()" class="add-source-code-cancel-button" name="rename-folder-cancel-button"><i class="fas fa-times"></i> <?php echo getTranslatedContent("account_cancel"); ?></a>
                    </div>
                </div>
            </div>
            <hr id="top-hr">
            <p class="service-interruption" style="text-align: center; color: var(--body-color); font-family: var(--font-family); background-color: var(--text-color); border-radius: 5px; padding: 1vw 0; position: relative; margin-left: 1vw;"><strong>IMPORTANTE:</strong> Il servizio verrà interrotto il giorno <strong>15/02/2020</strong>.<br>Sarà nei giorni successivi a questa data resa disponibile la nuova versione, e potrai quindi creare un nuovo account.<br><strong>Nota:</strong> Il trasferimento dei dati non è possibile si consiglia perciò di scaricare tutti i file prima della data sopra specificata.</p>
            <div class="folder-name-back-button-container">
                <h1 class="account-source-codes-h1"><?php if(!isset($_GET['folder'])) echo getTranslatedContent("account_my_source_codes"); else echo $GLOBALS['folderName']; ?></h1>
                <?php
                    if(isset($_GET['folder'])){
                        echo '<button class="folder-back-button" id="folder-back-button"><i class="fas fa-arrow-left"></i> '; echo getTranslatedContent("account_back"); echo'</button>';
                    }
                ?>
            </div>
            <div class="source-codes-container" id="source-codes-container">
                <div style="display: flex; width: 100%; flex-wrap: wrap;" id="folder-container">
                    <?php
                        if(!isset($_GET['view'])){
                            require("../php/dbh.php");
                            require("../php/get-num-of-files-in-folder.php");
                            require("../php/get-num-of-folders-in-folder.php");

                            $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                echo '';

                                exit();
                            }

                            mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                            mysqli_stmt_execute($stmt);

                            $result = mysqli_stmt_get_result($stmt);
                            $user = mysqli_fetch_assoc($result);

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

                            if(!isset($_GET['folder'])){
                                if(isset($_GET['order'])){
                                    $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folder=? ORDER BY ".$order." ".$d;
                                }
                                else{
                                    $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folder=? ORDER BY name";
                                }
                            }
                            else{
                                $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
                            }

                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                echo '';
        
                                exit();
                            }
                            
                            if(!isset($_GET['folder'])){
                                $path = "../u/".$user['userID'];
                                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $path);
                            }
                            else{
                                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['folder']);
                            }

                            mysqli_stmt_execute($stmt);

                            $folders = mysqli_stmt_get_result($stmt);

                            if(isset($_GET['folder'])){
                                $folder = mysqli_fetch_assoc($folders);

                                if(isset($_GET['order'])){
                                    $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folder=? ORDER BY ".$order." ".$d;
                                }
                                else{
                                    $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folder=? ORDER BY name";
                                }

                                $stmt = mysqli_stmt_init($conn);

                                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                    echo '';
            
                                    exit();
                                }

                                $path = $folder['pathToThis'];

                                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $path);

                                mysqli_stmt_execute($stmt);

                                $folders = mysqli_stmt_get_result($stmt);
                            }

                            $i = 0;

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
                                        <button class="folder-delete-button"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_folder_buttons_delete"); echo'</button>
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
                        }
                    ?>
                </div>
                <?php
                    if(!isset($_GET['view'])){
                        require("../php/dbh.php");

                        $sqlQuery = "SELECT * FROM users WHERE username=? OR email=?";
                        $stmt = mysqli_stmt_init($conn);

                        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                            echo '';

                            exit();
                        }

                        mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_SESSION['email']);
                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt);
                        $user = mysqli_fetch_assoc($result);

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

                        if(!isset($_GET['folder'])){
                            if(isset($_GET['order'])){
                                $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND folder=? ORDER BY ".$order." ".$d; 
                            }
                            else{
                                $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND folder=? ORDER BY name"; 
                            }
                        }
                        else{
                            $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
                        }

                        $stmt = mysqli_stmt_init($conn);

                        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                            echo '';

                            exit();
                        }
                        
                        if(!isset($_GET['folder'])){
                            $path = "../u/".$user['userID'];
                            mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $path);
                        }
                        else{
                            mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['folder']);
                        }

                        mysqli_stmt_execute($stmt);

                        if(!isset($_GET['folder'])){
                            $files = mysqli_stmt_get_result($stmt);
                        }
                        else{
                            $result = mysqli_stmt_get_result($stmt);
                            $folder = mysqli_fetch_assoc($result);

                            if(isset($_GET['order'])){
                                $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND folder=? ORDER BY ".$order." ".$d;
                            }
                            else{
                                $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND folder=? ORDER BY name";
                            }
                            
                            $stmt = mysqli_stmt_init($conn);

                            if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                echo '';

                                exit();
                            }

                            mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $folder['pathToThis']);
                            mysqli_stmt_execute($stmt);

                            $files = mysqli_stmt_get_result($stmt);
                        }

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
                                            <button class="source-code-button source-code-delete-button"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_source_code_buttons_delete"); echo'</button>
                                            <button class="source-code-button source-code-info-button"><i class="fas fa-info-circle"></i> '; echo getTranslatedContent("account_source_code_buttons_info"); echo'</button>
                                            <button class="source-code-button source-code-download-button"><a href="../download/?sid='; echo $file['fileID']; echo'" style="text-decoration: none; color: inherit;" download><i class="fas fa-download"></i> '; echo getTranslatedContent("account_source_code_buttons_download"); echo'</a></button>
                                        </div>
                                        <div class="source-code-share-div">
                                            <a href="" id="'; echo $file['fileID']; echo'" class="source-code-share-link">'; echo "https://denvelope.com/share/?sid=".$file['fileID']; echo'</a>
                                            <div class="source-code-copy-button">
                                                <i class="fas fa-copy"></i> Copy
                                            </div>
                                        </div>
                                        <div class="source-code-delete-div">
                                            <p style="margin-right: 1%;"><strong>Are you sure? You won\'t be able to revert this!</strong></p>
                                            <div class="source-code-delete-button-confirm" style="text-transform: uppercase;">
                                                <i class="fas fa-trash"></i> Yes, Delete This
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
                                                <strong>'; echo getTranslatedContent("account_source_code_info_size"); echo': </strong><small sort-data="size-info">'; echo $file['size']; echo'</small>
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
                                    <button class="source-code-delete-button"><i class="fas fa-trash"></i> '; echo getTranslatedContent("account_source_code_buttons_delete"); echo'</button>
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
                    }
                ?>
            </div>
        </div>
    </div>

    <div class="account-context-menu">
        <h3 id="source-code-view-button-cm" class="file-related-cm-option"><i class="fas fa-eye"></i> <?php echo getTranslatedContent("account_context_menu_view"); ?></h3>
        <h3 id="source-code-share-button-cm" class="file-related-cm-option"><i class="fas fa-share-alt"></i> <?php echo getTranslatedContent("account_context_menu_share"); ?></h3>
        <h3 id="source-code-rename-button-cm" class="file-related-cm-option"><i class="fas fa-edit"></i> <?php echo getTranslatedContent("account_context_menu_rename"); ?></h3>
        <h3 id="source-code-info-button-cm" class="file-related-cm-option"><i class="fas fa-info-circle"></i> <?php echo getTranslatedContent("account_context_menu_info"); ?></h3>
        <h3 id="source-code-download-button-cm" class="file-related-cm-option"><i class="fas fa-download"></i> <?php echo getTranslatedContent("account_context_menu_download"); ?></h3>
        <h3 id="source-code-delete-button-cm" class="file-related-cm-option"><i class="fas fa-trash"></i> <?php echo getTranslatedContent("account_context_menu_delete"); ?></h3>
        <hr>
        <h3 id="source-code-add-source-button-cm"><i class="fas fa-plus"></i> <?php echo getTranslatedContent("account_context_menu_add_source"); ?></h3>
        <h3 id="source-code-new-file-button-cm"><i class="fas fa-file-code"></i> <?php echo getTranslatedContent("account_context_menu_new_file"); ?></h3>
        <h3 id="source-code-new-folder-button-cm"><i class="fas fa-folder-plus"></i> <?php echo getTranslatedContent("account_context_menu_new_folder"); ?></h3>
    </div>

    <?php
        unset($_SESSION['fileDeleteSuccess']);
    ?>

    <div class="plus-button-div">
        <button class="plus-button" id="plus-button">
            <i class="fas fa-bars"></i>
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="ajax-file-upload-bar" id="ajax-file-upload-bar">
        <div class="cancel-upload">
            <button id="cancel-ajax-upload-button"><i class="fas fa-times"></i> <?php echo getTranslatedContent("account_cancel"); ?></button>
        </div>
        <div>
            <p>File / Folder Name</p>
            <span></span>
        </div>
    </div>

    <script>

        var contextMenuClickedItem;

        $(document).ready(function(){

            checkSingleFileName(true);
            checkProjectName(true);
            checkNewFileName(true);
            checkNewFolderName(true);
            blockSave("multiple");

            $("#plus-button").click(function(){
                if($("#top-div-account").css("display") != "none"){
                    $("#top-div-account").css("animation", "100ms menuClose");

                    setTimeout(() => {
                        $("#top-div-account").css("display", "none");
                        $("#plus-button i:last-child").css("display", "none");
                        $("#plus-button i:first-child").css("display", "unset");
                    }, 100);

                    if($(window).width() > 1000){
                        $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("left", "0");
                        $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("width", "calc(100% - 1vw)");
                    }
                }
                else{
                    $("#top-div-account").css("display", "flex");
                    $("#top-div-account").css("animation", "100ms menuOpen");

                    setTimeout(() => {
                        $("#plus-button i:first-child").css("display", "none");
                        $("#plus-button i:last-child").css("display", "unset");
                    }, 100);

                    if($(window).width() > 1200){
                        $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("left", "25%");
                        $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("width", "calc(75% - 1vw)");
                    }
                    else if($(window).width() > 1000){
                        $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("left", "30%");
                        $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("width", "calc(70% - 1vw)");
                    }
                }
            });

            $(window).resize(function(){
                if($(window).width() > 1200){
                    $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("left", "25%");
                    $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("width", "calc(75% - 1vw)");
                }
                else if($(window).width() > 1000){
                    $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("left", "30%");
                    $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("width", "calc(70% - 1vw)");
                }
                else{
                    $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("left", "0");
                    $(".folder-name-back-button-container, .source-codes-container, .service-interruption").css("width", "calc(100% - 1vw)");
                }
            });

            $("#modal").click(function(e){
                if(e.target.id == "modal"){
                    cancelAddSourceCode();
                    resetSingleFileName();
                    resetProjectName();
                    resetNewFileName(true);
                    resetNewFolderName(true);
                    blockSave("multiple");
                }
            });

            $(".add-source-code-back-button, .add-source-code-cancel-button").click(function(){
                resetSingleFileName();
                resetProjectName();
                resetNewFileName(true);
                resetNewFolderName(true);
                checkSingleFileName(true);
                checkProjectName(true);
                checkNewFileName(true);
                checkNewFolderName(true);
                blockSave("multiple");
            });

            $("#file-single").change(function(){
                if($("#file-single").val().length > 0){
                    $("#source-file-upload-label").html('<i class="fas fa-file"></i> <?php echo getTranslatedContent("account_single_file_file_added"); ?>');
                    $("#source-name").val($("#file-single")[0].files[0]['name']);
                    var fileSplit = $("#file-single")[0].files[0]['name'].split(".");
                    var ext = fileSplit[fileSplit.length - 1];

                    languageSelect(ext);

                    allowSave("single");
                }
                else{
                    blockSave("single");
                }
            });

            $("#file-project").change(function(){
                if($("#file-project").val().length > 0){
                    $("#source-folder-upload-label").html('<i class="fas fa-folder-open"></i> <?php echo getTranslatedContent("account_project_folder_added"); ?>');
                    $("#source-name-project").val($("#file-project")[0].files[0].webkitRelativePath.split("/")[0]);

                    var numOfFiles = $("#file-project")[0].files.length;

                    var langArray = [];
                    var pathArray = [];
                    
                    for(var i = 0; i < numOfFiles; i++){
                        pathArray[i] = $("#file-project")[0].files[i].webkitRelativePath;
                        langArray[i] = fileLanguage($("#file-project")[0].files[i].name);
                    }

                    $("#path-array").attr("value", JSON.stringify(pathArray));
                    $("#lang-array").attr("value", JSON.stringify(langArray));

                    allowSave("project");
                }
                else{
                    blockSave("project");
                }
            });

            $("#file-multiple").change(function(){
                if($("#file-multiple").val().length > 0){
                    var numOfFiles = $("#file-multiple")[0].files.length;

                    if(numOfFiles != 1){
                        $("#source-multiple-upload-label").html('<i class="fas fa-copy"></i> ' + numOfFiles + ' <?php echo getTranslatedContent("account_multiple_files_files_added"); ?>');
                    }
                    else{
                        $("#source-multiple-upload-label").html('<i class="fas fa-copy"></i> 1 <?php echo getTranslatedContent("account_multiple_files_file_added"); ?>');
                    }

                    var langArray = [];

                    for(var i = 0; i < numOfFiles; i++){
                        langArray[i] = fileLanguage($("#file-multiple")[0].files[i].name);
                    }

                    $("#lang-array-multiple").attr("value", JSON.stringify(langArray));

                    allowSave("multiple");

                    $("#multiple-files-error").html("");
                }
                else{
                    blockSave("multiple");
                }
            });

            $("#source-name").keyup(function(){
                checkSingleFileName(false);
            });

            $("#source-name-project").keyup(function(){
                checkProjectName(false);
            });

            $("#source-name-new-file").keyup(function(){
                checkNewFileName(false);
            });

            $("#source-name-new-folder").keyup(function(){
                checkNewFolderName(false);
            });

            $("#add-source-code-save-button-single").click(function(e){
                e.preventDefault();
                
                if($("#add-source-code-save-button-single").css("cursor") != "not-allowed"){
                    createFormDataFilesAJAXUpload($("#file-single")[0].files);
                }
            });

            $("#add-source-code-project-button").click(function(e){
                e.preventDefault();

                if($("#add-source-code-project-button").css("cursor") != "not-allowed"){
                    createFormDataFolderAJAXUpload($("#file-project")[0].files, $("#lang-array").val(), $("#path-array").val(), $("#source-name-project").val());
                }
            });

            $("#add-source-code-multiple-button").click(function(e){
                if($("#add-source-code-multiple-button").css("cursor") == "not-allowed"){
                    e.preventDefault();
                    $("#multiple-files-error").html("<?php echo getTranslatedContent("account_multiple_files_error_choose_some_files"); ?>");
                }
                else{
                    $("#multiple-files-error").html("");
                }
            });

            $("#add-source-code-new-file-button").click(function(e){
                if($("#add-source-code-new-file-button").css("cursor") == "not-allowed"){
                    e.preventDefault();
                }
            });

            $("#add-source-code-new-folder-button").click(function(e){
                if($("#add-source-code-new-folder-button").css("cursor") == "not-allowed"){
                    e.preventDefault();
                }
            });

            $(".source-code-delete-button").click(function(){
                $(this).parent().prev().children(".source-code-main-div").children(".source-code-delete-div").children(".source-code-delete-button-confirm").click();
            });

            $(".source-code-delete-button-confirm").click(function(){
                var id = $(this).parent().parent().children(".source-code-share-div").children(".source-code-share-link").attr("id");
                var idElementToDelete = $(this).parent().parent().parent().attr("id");

                $.ajax({
                    type: "POST",
                    url: "../php/delete-file.php",
                    data: "fileID=" + id,
                    dataType: "JSON",
                    success: function(r){
                        if(r[0]['fileDeleteSuccess'] == true){
                            $("#" + idElementToDelete + ", #" + idElementToDelete + " + .source-code-buttons-div-mob-buttons").remove();
                            $("#used-storage-span").html(r[0]['usedStorage']);
                            if(r[0]['emptyFolder']){
                                $("#empty-folder-div").css("display", "flex");
                            }
                            $("#success-msg p").html("<i class='fas fa-check'></i> <?php echo getTranslatedContent("account_message_file_deleted"); ?>");
                            $("#success-msg").css("display", "block");
                            setTimeout(function(){
                                $("#success-msg").css("display", "none")
                            }, 2500);
                        }
                    },
                    error: function(r){
                        $("#error-msg p").html("<i class='fas fa-times'></i> <?php echo getTranslatedContent("account_message_error_deleting_file"); ?>");
                        $("#error-msg").css("display", "block");
                        setTimeout(function(){
                            $("#error-msg").css("display", "none")
                        }, 2500);
                    }
                });
            });

            $(".source-code-copy-button").click(function(){
                var temp = $("<input></input>");
                $("#body").append(temp);
                temp.val($(this).parent().children(".source-code-share-link").html()).select();
                document.execCommand("copy");
                temp.remove();
            });

            $(".source-code-view-button").click(function(){
                if($(this).parent().attr("class") != "source-code-buttons-div-mob-buttons"){
                    var id = $(this).parent().parent().children(".source-code-share-div").children(".source-code-share-link").attr("id");
                }
                else{
                    var id = $(this).parent().prev().children(".source-code-main-div").children(".source-code-share-div").children(".source-code-share-link").attr("id");
                }

                window.location.href = "./?view=" + id;
            });

            $(".source-code-div").click(function(e){
                if(($(this).hasClass("follow-url") || !$(this).hasClass("dragging")) && !$(e.target).is(".source-code-buttons-div button, .source-code-buttons-div button i") && !$(e.target).is(".source-code-share-div, .source-code-delete-div, .source-code-info-div") && !$(e.target).is(".source-code-copy-button, .source-code-delete-button-confirm, .source-code-delete-button-confirm i") && !$(e.target).is(".source-code-info-div div, .source-code-info-div div strong, .source-code-info-div div small") && !$(e.target).is(".source-code-buttons-div-mob, .source-code-buttons-div-mob button, .source-code-buttons-div-mob button i") && !$(e.target).is(".source-code-download-button a")){
                    var id = $(this).children(".source-code-main-div").children(".source-code-share-div").children(".source-code-share-link").attr("id");

                    window.location.href = "./?view=" + id;
                }
                else if($(this).hasClass("dragging")){
                    $(this).removeClass("dragging");
                }
            });

            $(".folder-div").click(function(e){
                if(($(this).hasClass("follow-url") || !$(this).hasClass("dragging")) && !$(e.target).is(".folder-menu-button, .folder-menu-button i")){
                    var id = $(this).children("div").attr("id");

                    window.location.href = "./?folder=" + id;
                }
                else if($(this).hasClass("dragging")){
                    $(this).removeClass("dragging");
                }
            });

            $("#view-source-close-button").click(function(){
                <?php
                    $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND fileID=?";
                    $stmt = mysqli_stmt_init($conn);

                    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                        echo '';

                        exit();
                    }

                    mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['view']);
                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);
                    $file = mysqli_fetch_assoc($result);

                    if(count(explode("/", $file['folder'])) > 3){
                        $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis=?";
                        $stmt = mysqli_stmt_init($conn);

                        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                            echo '';

                            exit();
                        }

                        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $file['folder']);
                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt);
                        $folder = mysqli_fetch_assoc($result);

                        echo 'window.location.href = "./?folder='; echo $folder['folderID']; echo'";';
                    }
                    else{
                        echo 'window.location.href = "./"';
                    }
                ?>
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

            $("#view-source-edit-button").click(function(){
                if(editor.getOption("readOnly") == true){
                    editor.setReadOnly(false);
                    $("#view-source-edit-button").html("<i class='fas fa-times'></i> <?php echo getTranslatedContent("account_view_source_stop_editing") ?>");
                    $(".view-source-bottom-left").append("<button class='source-code-button' id='view-source-save-button'><i class='fas fa-save'></i> <?php echo getTranslatedContent("account_view_source_save") ?></button>");

                    $("#view-source-save-button").click(function(){

                        var content = editor.getSession().getValue();
                        var id = "<?php if(isset($_GET['view'])) echo $_GET['view']; ?>";

                        content = encodeURIComponent(content);

                        $.ajax({
                            type: "POST",
                            url: "../php/update-file-content.php",
                            data: "content=" + content + "&id=" + id,
                            dataType: "JSON",
                            success: function(r){
                                if(r[0]['fileUpdatedSuccess'] == true){
                                    $("#file-save-success-msg").css("display", "block");
                                    editor.setReadOnly(true);
                                    $("#view-source-edit-button").html("<i class='fas fa-edit'></i> <?php echo getTranslatedContent("account_view_source_edit") ?>");
                                    $("#view-source-save-button").remove();
                                    setTimeout(function(){
                                        $("#file-save-success-msg").css("display", "none")
                                    }, 2500);

                                    var lastModifiedUpdated = r[0]['lastModifiedSavedFile'];

                                    $("#last-modified-file").html(lastModifiedUpdated);
                                }
                            },
                            error: function(r){
                                $("#file-save-error-msg").css("display", "block");
                                editor.setReadOnly(true);
                                $("#view-source-edit-button").html("<i class='fas fa-edit'></i> <?php echo getTranslatedContent("account_view_source_edit") ?>");
                                $("#view-source-save-button").remove();
                                setTimeout(function(){
                                    $("#file-save-error-msg").css("display", "none")
                                }, 2500);
                            }
                        });
                    });
                }
                else{
                    editor.setReadOnly(true);
                    $("#view-source-edit-button").html("<i class='fas fa-edit'></i> <?php echo getTranslatedContent("account_view_source_edit") ?>");
                    $("#view-source-save-button").remove();
                }
            });

            $("#view-source-edit-button-mob").click(function(){
                if(editor.getOption("readOnly") == true){
                    editor.setReadOnly(false);
                    $("#view-source-edit-button-mob").html("<i class='fas fa-times'></i> <?php echo getTranslatedContent("account_view_source_stop_editing") ?>");
                    $("#view-source-menu-mob").append("<button class='source-code-button' id='view-source-save-button-mob'><i class='fas fa-save'></i> <?php echo getTranslatedContent("account_view_source_save") ?></button>");

                    $("#view-source-save-button-mob").click(function(){

                        var content = editor.getSession().getValue();
                        var id = "<?php if(isset($_GET['view'])) echo $_GET['view']; ?>";

                        content = encodeURIComponent(content);

                        $.ajax({
                            type: "POST",
                            url: "../php/update-file-content.php",
                            data: "content=" + content + "&id=" + id,
                            dataType: "JSON",
                            success: function(r){
                                if(r[0]['fileUpdatedSuccess'] == true){
                                    $("#file-save-success-msg").css("display", "block");
                                    editor.setReadOnly(true);
                                    $("#view-source-edit-button-mob").html("<i class='fas fa-edit'></i> <?php echo getTranslatedContent("account_view_source_edit") ?>");
                                    $("#view-source-save-button-mob").remove();
                                    setTimeout(function(){
                                        $("#file-save-success-msg").css("display", "none")
                                    }, 2500);

                                    var lastModifiedUpdated = r[0]['lastModifiedSavedFile'];

                                    $("#last-modified-file").html(lastModifiedUpdated);
                                }
                            },
                            error: function(r){
                                $("#file-save-error-msg").css("display", "block");
                                editor.setReadOnly(true);
                                $("#view-source-edit-button-mob").html("<i class='fas fa-edit'></i> <?php echo getTranslatedContent("account_view_source_edit") ?>");
                                $("#view-source-save-button-mob").remove();
                                setTimeout(function(){
                                    $("#file-save-error-msg").css("display", "none")
                                }, 2500);
                            }
                        });
                    });
                }
                else{
                    editor.setReadOnly(true);
                    $("#view-source-edit-button-mob").html("<i class='fas fa-edit'></i> <?php echo getTranslatedContent("account_view_source_edit") ?>");
                    $("#view-source-save-button-mob").remove();
                }
            });

            $("#source-code-view-button-cm").click(function(){
                if($("#" + contextMenuClickedItem).parent().attr("class") != "folder-div"){
                    $("#" + contextMenuClickedItem).children(".source-code-main-div").children(".source-code-buttons-div").children(".source-code-view-button").click();
                }
                else{
                    var id = $("#" + contextMenuClickedItem).attr("id");

                    window.location.href = "./?folder=" + id;
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

            $("#source-code-delete-button-cm").click(function(){
                if($("#" + contextMenuClickedItem).parent().attr("class") != "folder-div"){
                    $("#" + contextMenuClickedItem).children(".source-code-main-div").children(".source-code-delete-div").children(".source-code-delete-button-confirm").click();
                }
                else{
                    $("#" + contextMenuClickedItem).parent().next().children(".folder-delete-button").click();
                }
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

            $("#source-code-rename-button-cm").click(function(){
                if($("#" + contextMenuClickedItem).parent().attr("class") != "folder-div"){
                    var name = $("#" + contextMenuClickedItem).children(".source-code-main-div").children("h3").html();

                    $(".modal").css("display", "flex");
                    $("#source-code-rename-div").css("display", "flex");
                    $("#rename-file").val(unescapeHTML(name));
                    $("#rename-file").attr("file-id", $("#" + contextMenuClickedItem).children(".source-code-main-div").children(".source-code-share-div").children("a").attr("id"));
                    $("#modal-select").css("display", "none");
                    if($(window).width() <= 1200){
                        $("#header").css("display", "none");
                    }
                }
                else{
                    var name = $("#" + contextMenuClickedItem).children(".folder-main-div").children("h3").html();

                    $(".modal").css("display", "flex");
                    $("#folder-rename-div").css("display", "flex");
                    $("#rename-folder").val(unescapeHTML(name));
                    $("#rename-folder").attr("folder-id", contextMenuClickedItem);
                    $("#modal-select").css("display", "none");
                    if($(window).width() <= 1200){
                        $("#header").css("display", "none");
                    }
                }
            });

            $("#rename-file-button").click(function(){
                var id = $("#rename-file").attr("file-id");
                var name = $("#rename-file").val();

                $.ajax({
                    type: "POST",
                    url: "../php/rename-file.php",
                    data: "fileID=" + id + "&name=" + name,
                    dataType: "JSON",
                    success: function(r){
                        if(r[0]['fileRenamedCorrectly'] == true){
                            $("#success-msg p").html("<i class='fas fa-check'></i> <?php echo getTranslatedContent("account_message_file_renamed"); ?>");
                            $("#success-msg").css("display", "block");

                            name = r[0]['fileRenameName'];
                            var lastModified = r[0]['fileRenameLastModified'];

                            $("#" + id).parent().parent().children("h3").html(escapeHTML(name));
                            $(".modal").css("display", "none");
                            $("#source-code-rename-div").css("display", "none");
                            $("#" + id).parent().parent().children(".source-code-info-div").children("div:first-child").children("small[sort-data=name-info]").html(escapeHTML(name));

                            $("#" + id).parent().parent().children(".source-code-info-div").children("div").children("small[sort-data=last-mod-info]").html(lastModified);

                            setTimeout(function(){
                                $("#success-msg").css("display", "none")
                            }, 2500);
                        }
                        else{
                            $("#error-msg p").html("<i class='fas fa-times'></i> <?php echo getTranslatedContent("account_message_error_renaming_file"); ?>");
                            $("#error-msg").css("display", "block");
                            setTimeout(function(){
                                $("#error-msg").css("display", "none")
                            }, 2500);
                        }
                    },
                    error: function(r){
                        $("#error-msg p").html("<i class='fas fa-times'></i> <?php echo getTranslatedContent("account_message_error_renaming_file"); ?>");
                        $("#error-msg").css("display", "block");
                        setTimeout(function(){
                            $("#error-msg").css("display", "none")
                        }, 2500);
                    }
                });
            });

            $("#rename-folder-button").click(function(){
                var id = $("#rename-folder").attr("folder-id");
                var name = $("#rename-folder").val();

                $.ajax({
                    type: "POST",
                    url: "../php/rename-folder.php",
                    data: "folderID=" + id + "&name=" + name,
                    dataType: "JSON",
                    success: function(r){
                        if(r[0]['folderRenamedCorrectly'] == true){
                            $("#success-msg p").html("<i class='fas fa-check'></i> <?php echo getTranslatedContent("account_message_folder_renamed"); ?>");
                            $("#success-msg").css("display", "block");

                            name = r[0]['folderRenameName'];
                            var lastModified = r[0]['folderRenameLastModified'];

                            $("#" + id).children(".folder-main-div").children("h3").html(escapeHTML(name));
                            $(".modal").css("display", "none");
                            $("#folder-rename-div").css("display", "none");
                            $("#" + id + " + .folder-info-div small[sort-data=name-info]").html(escapeHTML(name));

                            $("#" + id + " + .folder-info-div small[sort-data=last-mod-info]").html(lastModified);

                            setTimeout(function(){
                                $("#success-msg").css("display", "none")
                            }, 2500);
                        }
                        else{
                            $("#error-msg p").html("<i class='fas fa-times'></i> <?php echo getTranslatedContent("account_message_error_renaming_folder"); ?>");
                            $("#error-msg").css("display", "block");
                            setTimeout(function(){
                                $("#error-msg").css("display", "none")
                            }, 2500);
                        }
                    },
                    error: function(r){
                        $("#error-msg p").html("<i class='fas fa-times'></i> <?php echo getTranslatedContent("account_message_error_renaming_folder"); ?>");
                        $("#error-msg").css("display", "block");
                        setTimeout(function(){
                            $("#error-msg").css("display", "none")
                        }, 2500);
                    }
                });
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

            $("#source-code-download-button-cm").click(function(){
                if($("#" + contextMenuClickedItem).parent().attr("class") != "folder-div"){
                    $("#" + contextMenuClickedItem).children(".source-code-main-div").children(".source-code-buttons-div").children(".source-code-download-button").children("a")[0].click();
                }
                else{
                    $("#" + contextMenuClickedItem).parent().next(".folder-div-mob-buttons").children(".folder-download-button").children("a")[0].click();
                }
            });

            $("#view-source-download-button").click(function(){
                $(this).children("a")[0].click();
            });

            $("#view-source-download-button-mob").click(function(){
                $(this).children("a")[0].click();
            });

            $("#source-code-add-source-button-cm").click(function(){
                $("#add-source-code-button").click();
            });

            $("#source-code-new-file-button-cm").click(function(){
                $("#new-file-button").click();
            });

            $("#source-code-new-folder-button-cm").click(function(){
                $("#new-folder-button").click();
            });

            $("#new-file-button").click(function(){
                $("#modal").css("display", "flex");
                $("#modal-select").css("display", "none");
                $("#modal-content").css("display", "flex");
                $("#new-file-form").css("display", "flex");

                checkNewFileName(true);

                if($(window).outerWidth() <= 1200){
                    $("#header").css("display", "none");
                }
            });

            $("#new-folder-button").click(function(){
                $("#modal").css("display", "flex");
                $("#modal-select").css("display", "none");
                $("#modal-content").css("display", "flex");
                $("#new-folder-form").css("display", "flex");

                checkNewFolderName(true);

                if($(window).outerWidth() <= 1200){
                    $("#header").css("display", "none");
                }
            });

            $(".source-code-buttons-div-mob button").click(function(e){
                $(".file-related-cm-option").css("display", "block");
                $(".account-context-menu h3:not(.file-related-cm-option)").css("display", "none");

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
                    
                positionContextMenu(e);
            });

            $(".folder-view-button").click(function(){
                $(this).parent().prev().click();
            });

            $(".folder-delete-button").click(function(){
                var id = $(this).parent().prev().children("div:first-child").attr("id");
                var idElementToDelete = $(this).parent().prev().attr("id");

                $.ajax({
                    type: "POST",
                    url: "../php/delete-folder.php",
                    data: "folderID=" + id,
                    dataType: "JSON",
                    success: function(r){
                        if(r[0]['folderDeleteSuccess'] == true){
                            $("#" + idElementToDelete + ", #" + idElementToDelete + " + .folder-div-mob-buttons").remove();
                            $("#used-storage-span").html(r[0]['usedStorage']);
                            if(r[0]['emptyFolder']){
                                $("#empty-folder-div").css("display", "flex");
                            }
                            $("#success-msg p").html("<i class='fas fa-check'></i> <?php echo getTranslatedContent("account_message_folder_deleted"); ?>");
                            $("#success-msg").css("display", "block");
                            setTimeout(function(){
                                $("#success-msg").css("display", "none")
                            }, 2500);
                        }
                    },
                    error: function(r){
                        $("#error-msg p").html("<i class='fas fa-times'></i> <?php echo getTranslatedContent("account_message_error_deleting_folder"); ?>");
                        $("#error-msg").css("display", "block");
                        setTimeout(function(){
                            $("#error-msg").css("display", "none")
                        }, 2500);
                    }
                });
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

            $(".source-code-buttons-div button").mouseenter(function(){
                $(this).css("color", "var(--header-color)");
                $(this).css("background-color", "var(--text-color)");
            });

            $(".source-code-buttons-div button").mouseleave(function(){
                $(this).css("color", "var(--text-color)");
                $(this).css("background-color", "var(--header-color)");
            });

            $("#view-source-delete-button, #view-source-delete-button-mob").click(function(){
                var id = "<?php if(isset($_GET['view'])) echo $_GET['view']; ?>";

                $.ajax({
                    type: "POST",
                    url: "../php/delete-file.php",
                    data: "fileID=" + id,
                    dataType: "JSON",
                    success: function(r){
                        if(r[0]['fileDeleteSuccess'] == true){
                            <?php
                                $sqlQuery = "SELECT * FROM files WHERE (usernameAuthor=? OR emailAuthor=?) AND fileID=?";
                                $stmt = mysqli_stmt_init($conn);

                                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                    echo '';

                                    exit();
                                }

                                mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['view']);
                                mysqli_stmt_execute($stmt);

                                $result = mysqli_stmt_get_result($stmt);
                                $file = mysqli_fetch_assoc($result);

                                if(count(explode("/", $file['folder'])) > 3){
                                    $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis=?";
                                    $stmt = mysqli_stmt_init($conn);

                                    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                                        echo '';

                                        exit();
                                    }

                                    mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $file['folder']);
                                    mysqli_stmt_execute($stmt);

                                    $result = mysqli_stmt_get_result($stmt);
                                    $folder = mysqli_fetch_assoc($result);

                                    echo 'window.location.href = "./?folder='; echo $folder['folderID']; echo'";';
                                }
                                else{
                                    echo 'window.location.href = "./"';
                                }
                            ?>
                        }
                    },
                    error: function(r){
                        
                    }
                });
            });

            $("#folder-back-button").click(function(){
                <?php
                    $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND folderID=?";
                    $stmt = mysqli_stmt_init($conn);

                    if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                        echo '';

                        exit();
                    }

                    mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $_GET['folder']);
                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);
                    $folder = mysqli_fetch_assoc($result);

                    if(count(explode("/", $folder['folder'])) > 3){
                        $sqlQuery = "SELECT * FROM folders WHERE (usernameAuthor=? OR emailAuthor=?) AND pathToThis=?";
                        $stmt = mysqli_stmt_init($conn);

                        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                            echo '';

                            exit();
                        }

                        mysqli_stmt_bind_param($stmt, "sss", $_SESSION['username'], $_SESSION['email'], $folder['folder']);
                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt);
                        $folder = mysqli_fetch_assoc($result);

                        echo 'window.location.href = "./?folder='; echo $folder['folderID']; echo'"';
                    }
                    else{
                        echo 'window.location.href = "./"';
                    }
                ?>
            });

            $(".folder-menu-button").click(function(e){
                $(".file-related-cm-option").css("display", "block");
                $(".account-context-menu h3:not(.file-related-cm-option)").css("display", "none");

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
                    
                positionContextMenu(e);
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

            $(".view-source-bottom-menu-mob").click(function(){
                if($("#view-source-menu-mob").css("display") == "none"){
                    $("#view-source-menu-mob").css("display", "block");
                }
                else{
                    $("#view-source-menu-mob").css("display", "none");
                }
            });

            $("#sort-by").change(function(){
                var order = $("#sort-by")[0][$("#sort-by")[0].selectedIndex].value;

                var url = window.location.href;
                var param = url.split("?");

                if(param.length > 1){
                    if(url.split("&").length > 1){
                        if(!url.includes("order=")){
                            window.location.href = url.substr(0, url.lastIndexOf("&")) + "&order=" + order;
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

            /*

            $(".source-code-div, .folder-div").on("mousedown", function(e){
                if(e.which != 3){
                    var element = $(this);

                    element.addClass("dragging");

                    if(!$(e.target).is(".source-code-button, .source-code-button i") && !$(e.target).is(".folder-menu-button, .folder-menu-button i")){
                        yOffset = e.pageY - element.offset().top;
                        xOffset = e.pageX - element.offset().left;

                        $(document).on("mousemove", function(e){
                            y = e.pageY - yOffset;
                            x = e.pageX - xOffset;

                            if(element.hasClass("dragging")){
                                element.offset({
                                    top: y,
                                    left: x
                                });
                            }
                        });
                    }
                }
            }).on("mouseup", function(){
                var element = $("div.dragging");

                if(element.css("position") != "relative"){
                    element.addClass("follow-url");
                }

                element.css({
                    "position": "unset",
                    "top": "unset",
                    "left": "unset"
                });
            });

            $(".folder-div").mouseover(function(){
                var element = $(".source-codes-container").find("div.dragging");

                if(element.length === 1 && $(element).attr("id").indexOf("scd") >= 0){ //checks whether the dragged element is a file
                    $(this).addClass("dragging-target");

                    console.log(element.find(".source-code-h3").html() + " -> " + $(this).find(".folder-h3").html());
                }
            }).mouseleave(function(){
                $(this).removeClass("dragging-target");
            });

            */

            $(document).on("drag dragstart dragend dragover dragenter dragleave drop", function(e){
                e.preventDefault();
                e.stopPropagation();
            });

            var formData = new FormData();
            var isDir = false;
            
            $(document).on("drop", function(e){
                formData = new FormData();
                var items = e.originalEvent.dataTransfer.items;

                isDir = false;

                for (var i = 0; i < items.length; i++) {
                    var item = items[i].webkitGetAsEntry();
                    if (item) {
                        traverseFileTree(item);
                    }
                }

                if(!isDir && items.length >= 1){
                    //single file / multiple files
                    createFormDataFilesAJAXUpload(e.originalEvent.dataTransfer.files);
                }
            });

            function traverseFileTree(item, path) {
                path = path || "";

                if(displayNameFolder == ""){
                    displayNameFolder = path.substr(0, path.length - 1); //removes the trailing '/' char
                }

                if (item.isFile && isDir) {
                    item.file(function(file) {
                        appendFileFolderAJAXUpload(file, path);
                    });
                } else if (item.isDirectory) {
                    var dirReader = item.createReader();

                    if(!isDir){
                        countFilesInDir(item);
                    }

                    isDir = true;

                    dirReader.readEntries(function(entries) {
                        for (var i = 0; i < entries.length; i++) {
                            traverseFileTree(entries[i], path + item.name + "/");
                        }
                    });
                }
            }

            var numOfFilesToUpload = 0;

            function countFilesInDir(item){
                if(item.isFile){
                    numOfFilesToUpload++;
                } else if (item.isDirectory) {
                    var dirReader = item.createReader();

                    dirReader.readEntries(function(entries) {
                        for (var i = 0; i < entries.length; i++) {
                            countFilesInDir(entries[i]);
                        }
                    });
                }
            }
            
            var fileCounter = 0;
            var displayNameFolder = "";

            function appendFileFolderAJAXUpload(file, path){
                formData.append("file-project[]", file, file.name);
                formData.append("lang-array", fileLanguage(file.name));
                formData.append("path-array", path + file.name);

                fileCounter++;

                if(fileCounter == numOfFilesToUpload){ //when all of the files have been added to formData
                    var langArray = formData.getAll("lang-array");
                    formData.delete("lang-array");
                    formData.append("lang-array", JSON.stringify(langArray));

                    var pathArray = formData.getAll("path-array");
                    formData.delete("path-array");
                    formData.append("path-array", JSON.stringify(pathArray));

                    formData.append("source-name-project", displayNameFolder);

                    AJAXFileUpload(formData, displayNameFolder);
                }
            }

            function createFormDataFilesAJAXUpload(files){
                if(files.length == 1){ //single file
                    formData.append("file-single", files[0], files[0].name);
                    formData.append("source-name", files[0].name);
                    formData.append("language", fileLanguage(files[0].name));

                    displayName = files[0].name;
                }
                else if(files.length > 1){
                    for(let i = 0; i < files.length; i++){
                        formData.append("file-multiple[]", files[i], files[i].name);
                        formData.append("lang-array-multiple", fileLanguage(files[i].name));
                    }

                    var langArray = formData.getAll("lang-array-multiple");
                    formData.delete("lang-array-multiple");
                    formData.append("lang-array-multiple", JSON.stringify(langArray));

                    displayName = ""; //in the future maybe an upload per file?
                }

                AJAXFileUpload(formData, displayName);
            }

            function createFormDataFolderAJAXUpload(files, langArray, pathArray, displayName){
                for(let i = 0; i < files.length; i++){
                    formData.append("file-project[]", files[i], files[i].name);
                }

                formData.append("lang-array", JSON.stringify(langArray));

                formData.append("path-array", JSON.stringify(pathArray));

                formData.append("source-name-project", displayName);

                AJAXFileUpload(formData, displayName);
            }

            function AJAXFileUpload(formData, displayName){
                formData.append("path-to-this", $("input[name=path-to-this]").val());

                //reset vars
                fileCounter = 0;
                numOfFilesToUpload = 0;
                displayNameFolder = "";

                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();

                        xhr.upload.addEventListener("progress", function(e) {
                            if (e.lengthComputable) {
                                var percentComplete = e.loaded / e.total;
                                percentComplete = parseInt(percentComplete * 100);

                                $("#ajax-file-upload-bar").css("display", "flex");

                                $("#ajax-file-upload-bar p").html(displayName);
                                $("#ajax-file-upload-bar span").css("width", percentComplete + "%");

                                if (percentComplete === 100) {
                                    $("#ajax-file-upload-bar p").html("<?php echo getTranslatedContent("account_message_ajax_file_upload_waiting_for_response"); ?>");
                                }
                            }
                        }, false);

                        $("#cancel-ajax-upload-button").click(function(){
                            xhr.abort();

                            $("#ajax-file-upload-bar").css("display", "none");
                        });

                        return xhr;
                    },
                    url: "../php/add-source-code.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    enctype: 'multipart/form-data',
                    success: function(r){
                        if(r['uploadSuccess'] == true){
                            $("#ajax-file-upload-bar").css("display", "none");

                            location.reload();

                            //TODO
                            //remove location.reload()
                            //create new source code / folder div and fill it with info
                        }
                    },
                    error: function(r){

                    }
                });
            }

            function fileLanguage(fileName){
                var lang;

                var ext = fileName.split(".")[fileName.split(".").length - 1];

                switch (ext) {
                    case "c": //C
                    case "h":
                        lang = "c||c_cpp||C";
                        break;
                    case "c++": //C++
                    case "cc":
                    case "cpp":
                    case "cxx":
                    case "h++":
                    case "hh":
                    case "hpp":
                    case "hxx":
                        lang = "cplusplus||c_cpp||C++";
                        break;
                    case "cs": //C#
                    case "csproj":
                        lang = "csharp||csharp||C#";
                        break;
                    case "css": //CSS
                        lang = "css3||css||CSS";
                        break;
                    case "go": //Go
                        lang = "go||golang||Go";
                        break;
                    case "htm": //HTML
                    case "html":
                    case "shtm":
                    case "shtml":
                        lang = "html5||html||HTML";
                        break;
                    case "class": //Java
                    case "jar":
                    case "java":
                        lang = "java||java||Java";
                        break;
                    case "js": //Javascript
                        lang = "javascript||javascript||Javascript";
                        break;
                    case "php": //PHP
                    case "php2":
                    case "php3":
                    case "php4":
                    case "php5":
                        lang = "php||php||PHP";
                        break;
                    case "py": //Python
                    case "pyc":
                    case "pyd":
                    case "pyo":
                    case "pyw":
                        lang = "python||python||Python";
                        break;
                    case "rs": //Rust
                    case "rlib":
                        lang = "rust-svg||rust||Rust";
                        break;
                    case "sql": //SQL
                        lang = "mysql||sql||SQL";
                        break;
                    case "swift": //Swift
                        lang = "swift||swift||Swift";
                        break;
                    case "txt": //Text File
                        lang = "other||plain_text||text_file";
                        break;
                    case "7z": //other_archive
                    case "rar":
                    case "tar":
                    case "zip":
                        lang = "other||other_archive||other_archive";
                        break;
                    case "aac": //other_audio
                    case "aiff":
                    case "flac":
                    case "mp3":
                    case "ogg":
                    case "wav":
                        lang = "other||other_audio||other_audio";
                        break;
                    case "doc": //other_document
                    case "docx": 
                    case "odt": 
                    case "pdf": 
                        lang = "other||other_document||other_document";
                        break;
                    case "gif": //other_image
                    case "ico":
                    case "jpeg":
                    case "jpg":
                    case "png":
                    case "svg":
                    case "tiff":
                        lang = "other||other_image||other_image";
                        break;
                    case "avi": //other_video
                    case "mkv":
                    case "mov":
                    case "mp4":
                    case "mpeg":
                    case "mpeg2":
                    case "ts":
                    case "wmv":
                        lang = "other||other_video||other_video";
                        break;
                    default:
                        lang = "other||plain_text||other";
                        break;
                }

                return lang;
            }

            function languageSelect(ext){
                switch (ext) {
                    case "c": //C
                    case "h":
                        $("#language")[0].selectedIndex = "1";
                        $("#language-new-file")[0].selectedIndex = "1";
                        break;
                    case "c++": //C++
                    case "cc":
                    case "cpp":
                    case "cxx":
                    case "h++":
                    case "hh":
                    case "hpp":
                    case "hxx":
                        $("#language")[0].selectedIndex = "2";
                        $("#language-new-file")[0].selectedIndex = "2";
                        break;
                    case "cs": //C#
                    case "csproj":
                        $("#language")[0].selectedIndex = "3";
                        $("#language-new-file")[0].selectedIndex = "3";
                        break;
                    case "css": //CSS
                        $("#language")[0].selectedIndex = "4";
                        $("#language-new-file")[0].selectedIndex = "4";
                        break;
                    case "go": //Go
                        $("#language")[0].selectedIndex = "5";
                        $("#language-new-file")[0].selectedIndex = "5";
                        break;
                    case "htm": //HTML
                    case "html":
                    case "shtm":
                    case "shtml":
                        $("#language")[0].selectedIndex = "6";
                        $("#language-new-file")[0].selectedIndex = "6";
                        break;
                    case "class": //Java
                    case "jar":
                    case "java":
                        $("#language")[0].selectedIndex = "7";
                        $("#language-new-file")[0].selectedIndex = "7";
                        break;
                    case "js": //Javascript
                        $("#language")[0].selectedIndex = "8";
                        $("#language-new-file")[0].selectedIndex = "8";
                        break;
                    case "php": //PHP
                    case "php2":
                    case "php3":
                    case "php4":
                    case "php5":
                        $("#language")[0].selectedIndex = "9";
                        $("#language-new-file")[0].selectedIndex = "9";
                        break;
                    case "py": //Python
                    case "pyc":
                    case "pyd":
                    case "pyo":
                    case "pyw":
                        $("#language")[0].selectedIndex = "10";
                        $("#language-new-file")[0].selectedIndex = "10";
                        break;
                    case "rs": //Rust
                    case "rlib":
                        $("#language")[0].selectedIndex = "11";
                        $("#language-new-file")[0].selectedIndex = "11";
                        break;
                    case "sql": //SQL
                        $("#language")[0].selectedIndex = "12";
                        $("#language-new-file")[0].selectedIndex = "12";
                        break;
                    case "swift": //Swift
                        $("#language")[0].selectedIndex = "13";
                        $("#language-new-file")[0].selectedIndex = "13";
                        break;
                    case "txt": //Text File
                        $("#language")[0].selectedIndex = "14";
                        $("#language-new-file")[0].selectedIndex = "14";
                        break;
                    case "7z": //other_archive
                    case "rar":
                    case "tar":
                    case "zip":
                        $("#language")[0].selectedIndex = "16";
                        break;
                    case "aac": //other_audio
                    case "aiff":
                    case "flac":
                    case "mp3":
                    case "ogg":
                    case "wav":
                        $("#language")[0].selectedIndex = "17";
                        break;
                    case "doc": //other_document
                    case "docx": 
                    case "odt": 
                    case "pdf": 
                    case "txt":
                        $("#language")[0].selectedIndex = "18";
                        break;
                    case "gif": //other_image
                    case "ico":
                    case "jpeg":
                    case "jpg":
                    case "png":
                    case "svg":
                    case "tiff":
                        $("#language")[0].selectedIndex = "19";
                        break;
                    case "avi": //other_video
                    case "mkv":
                    case "mov":
                    case "mp4":
                    case "mpeg":
                    case "mpeg2":
                    case "ts":
                    case "wmv":
                        $("#language")[0].selectedIndex = "20";
                        break;
                    default:
                        $("#language")[0].selectedIndex = "15";
                        $("#language-new-file")[0].selectedIndex = "15";
                        break;
                }
            }

            function checkSingleFileName(justClicked){
                var singleFileName = $("#source-name").val();

                if(singleFileName.length == 0){
                    if(!justClicked){
                        $("#source-name").css("background-color", "var(--text-color)");
                        $("#source-name").css("color", "var(--header-color)");
                        $("#source-name").addClass("error-placeholder");
                        $("#single-file-name-error").html("<br>Please enter a valid file name");
                        $("#language")[0].selectedIndex = "0";   
                    } 
                    $("#add-source-code-save-button-single").css("cursor", "not-allowed");
                    $("#add-source-code-save-button-single").css("opacity", "0.7");
                    $("#add-source-code-save-button-single").addClass("nohover");
                }
                else{
                    resetSingleFileName();

                    if(singleFileName.indexOf(".") >= 0){
                        var fileSplit = singleFileName.split(".");
                        var ext = fileSplit[fileSplit.length - 1];

                        languageSelect(ext);
                    }
                    else{
                        $("#language")[0].selectedIndex = "14";

                        if(singleFileName.length == 0){
                            $("#language")[0].selectedIndex = "0";
                        }
                    }

                    if($("#file-single").val().length > 0){
                        $("#add-source-code-save-button-single").css("cursor", "pointer");
                        $("#add-source-code-save-button-single").css("opacity", "1");
                        $("#add-source-code-save-button-single").removeClass("nohover");
                    }
                }
            }

            function checkProjectName(justClicked){
                var projectName = $("#source-name-project").val();

                if(projectName.length == 0){
                    if(!justClicked){
                        $("#source-name-project").css("background-color", "var(--text-color)");
                        $("#source-name-project").css("color", "var(--header-color)");
                        $("#source-name-project").addClass("error-placeholder");
                        $("#project-name-error").html("<br>Please enter a valid folder name"); 
                    } 
                    $("#add-source-code-project-button").css("cursor", "not-allowed");
                    $("#add-source-code-project-button").css("opacity", "0.7");
                    $("#add-source-code-project-button").addClass("nohover");
                }
                else{
                    resetProjectName();

                    if($("#file-project").val().length > 0){
                        $("#add-source-code-project-button").css("cursor", "pointer");
                        $("#add-source-code-project-button").css("opacity", "1");
                        $("#add-source-code-project-button").removeClass("nohover");
                    }
                }
            }

            function checkNewFileName(justClicked){
                var newFileName = $("#source-name-new-file").val();

                if(newFileName.length == 0){
                    if(!justClicked){
                        $("#source-name-new-file").css("background-color", "var(--text-color)");
                        $("#source-name-new-file").css("color", "var(--header-color)");
                        $("#source-name-new-file").addClass("error-placeholder");
                        $("#new-file-name-error").html("<br>Please enter a valid file name");
                        $("#language-new-file")[0].selectedIndex = "0";   
                    } 
                    $("#add-source-code-new-file-button").css("cursor", "not-allowed");
                    $("#add-source-code-new-file-button").css("opacity", "0.7");
                    $("#add-source-code-new-file-button").addClass("nohover");
                }
                else{
                    resetNewFileName(false);

                    if(newFileName.indexOf(".") >= 0){
                        var fileSplit = newFileName.split(".");
                        var ext = fileSplit[fileSplit.length - 1];

                        languageSelect(ext);
                    }
                    else{
                        $("#language-new-file")[0].selectedIndex = "14";

                        if(newFileName.length == 0){
                            $("#language-new-file")[0].selectedIndex = "0";
                        }
                    }
                }
            }

            function checkNewFolderName(justClicked){
                var newFolderName = $("#source-name-new-folder").val();

                if(newFolderName.length == 0){
                    if(!justClicked){
                        $("#source-name-new-folder").css("background-color", "var(--text-color)");
                        $("#source-name-new-folder").css("color", "var(--header-color)");
                        $("#source-name-new-folder").addClass("error-placeholder");
                        $("#new-folder-name-error").html("<br>Please enter a valid folder name"); 
                    } 
                    $("#add-source-code-new-folder-button").css("cursor", "not-allowed");
                    $("#add-source-code-new-folder-button").css("opacity", "0.7");
                    $("#add-source-code-new-folder-button").addClass("nohover");
                }
                else{
                    resetNewFolderName(false);
                }
            }

            function resetSingleFileName(){
                $("#source-name").css("background-color", "var(--header-color)");
                $("#source-name").css("color", "var(--text-color)");
                $("#source-name").removeClass("error-placeholder");
                $("#single-file-name-error").html("");
            }

            function resetProjectName(){
                $("#source-name-project").css("background-color", "var(--header-color)");
                $("#source-name-project").css("color", "var(--text-color)");
                $("#source-name-project").removeClass("error-placeholder");
                $("#project-name-error").html("");
            }

            function resetNewFileName(fromModalClose){
                $("#source-name-new-file").css("background-color", "var(--header-color)");
                $("#source-name-new-file").css("color", "var(--text-color)");
                $("#source-name-new-file").removeClass("error-placeholder");
                $("#new-file-name-error").html("");
                if(!fromModalClose){
                    $("#add-source-code-new-file-button").css("cursor", "pointer");
                    $("#add-source-code-new-file-button").css("opacity", "1");
                    $("#add-source-code-new-file-button").removeClass("nohover");
                }
            }

            function resetNewFolderName(fromModalClose){
                $("#source-name-new-folder").css("background-color", "var(--header-color)");
                $("#source-name-new-folder").css("color", "var(--text-color)");
                $("#source-name-new-folder").removeClass("error-placeholder");
                $("#new-folder-name-error").html("");
                if(!fromModalClose){
                    $("#add-source-code-new-folder-button").css("cursor", "pointer");
                    $("#add-source-code-new-folder-button").css("opacity", "1");
                    $("#add-source-code-new-folder-button").removeClass("nohover");
                }
            }

            function allowSave(which){
                switch (which) {
                    case "single":
                        $("#add-source-code-save-button-single").css("cursor", "pointer");
                        $("#add-source-code-save-button-single").css("opacity", "1");
                        $("#add-source-code-save-button-single").removeClass("nohover");
                        break;
                    case "project":
                        $("#add-source-code-project-button").css("cursor", "pointer");
                        $("#add-source-code-project-button").css("opacity", "1");
                        $("#add-source-code-project-button").removeClass("nohover");
                        break;
                    case "multiple":
                        $("#add-source-code-multiple-button").css("cursor", "pointer");
                        $("#add-source-code-multiple-button").css("opacity", "1");
                        $("#add-source-code-multiple-button").removeClass("nohover");
                        break;
                    default:
                        break;
                }
            }

            function blockSave(which){
                switch (which) {
                    case "single":
                        $("#add-source-code-save-button-single").css("cursor", "not-allowed");
                        $("#add-source-code-save-button-single").css("opacity", "0.7");
                        $("#add-source-code-save-button-single").addClass("nohover");

                        $("#source-name").val("");
                        $("#language").val(0);
                        $("#file-single").val(null);
                        $("#source-file-upload-label").html('<i class="fas fa-file-upload"></i> <?php echo getTranslatedContent("account_single_file_add_file"); ?>');
                        break;
                    case "project":
                        $("#add-source-code-project-button").css("cursor", "not-allowed");
                        $("#add-source-code-project-button").css("opacity", "0.7");
                        $("#add-source-code-project-button").addClass("nohover");

                        $("#file-project").val(null);
                        $("#source-folder-upload-label").html('<i class="fas fa-folder"></i> <?php echo getTranslatedContent("account_project_add_folder"); ?>');
                        $("#source-name-project").val("");
                        break;
                    case "multiple":
                        $("#add-source-code-multiple-button").css("cursor", "not-allowed");
                        $("#add-source-code-multiple-button").css("opacity", "0.7");
                        $("#add-source-code-multiple-button").addClass("nohover");

                        $("#file-multiple").val(null);
                        $("#source-multiple-upload-label").html('<i class="fas fa-copy"></i> <?php echo getTranslatedContent("account_multiple_files_add_files"); ?>');
                        break;
                    default:
                        break;
                }
            }

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
                    if($(this).find(elementToFind).html().toLowerCase().indexOf(input) == -1 && $(this).find(elementToFind).html().toLowerCase().indexOf(escapeHTML(input)) == -1){
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
                        $(this).attr("style", "margin-left: 0 !important");
                    }
                    else{
                        $(this).attr("style", "margin-left: 5% !important");
                    }
                });

                $(".source-code-div:not(.hidden)").each(function(i, obj){
                    if((i % 3 == 0) || i == 0){
                        $(this).attr("style", "margin-left: 0 !important");
                    }
                    else{
                        $(this).attr("style", "margin-left: 5% !important");
                    }
                });
            }

            function escapeHTML(string){
                string = string.replace(/\&/g, '&amp;');
                string = string.replace(/\>/g, '&gt;');
                string = string.replace(/\</g, '&lt;');
                string = string.replace(/\"/g, '&quot;');
                string = string.replace(/\'/g, "&#039;");

                return string;
            }

            function unescapeHTML(string){
                string = string.replace(/&amp;/g, '&');
                string = string.replace(/&gt;/g, '>');
                string = string.replace(/&lt;/g, '<');
                string = string.replace(/&quot;/g, '"');
                string = string.replace(/&#039;/g, "'");

                return string;
            }
        });

        $("#main-div-account").css("margin-top", $("#header").outerHeight(true));
        
        $("html").css("height", "calc(100% - " + $("#header").outerHeight(true) + "px)");
        
        if($(window).width() > 1200){
            $(".view-source-code").css("max-height", "calc(100vh - " + ($(".view-source-head").outerHeight(true) + $(".view-source-bottom").outerHeight(true)) + "px - 2vw - 2vw)");
        }
        else{
            $(".view-source-code").css("max-height", "calc(100% - " + ($(".view-source-head").outerHeight(true) + $("#view-source-bottom-menu-button").outerHeight(true)) + "px - 0vw - 2vw - 0px)");
        }

        $(window).on("load", function(){
            $("#main-div-account").css("margin-top", $("#header").outerHeight(true));
            $("html").css("height", "calc(100% - " + $("#header").outerHeight(true) + "px)");
            
            if($(window).width() > 1200){
                $(".view-source-code").css("max-height", "calc(100vh - " + ($(".view-source-head").outerHeight(true) + $(".view-source-bottom").outerHeight(true)) + "px - 2vw - 2vw)");
            }
            else{
                $(".view-source-code").css("max-height", "calc(100% - " + ($(".view-source-head").outerHeight(true) + $("#view-source-bottom-menu-button").outerHeight(true)) + "px - 0vw - 2vw - 0px)");
            }
        });

        $(window).resize(function(){
            $("#main-div-account").css("margin-top", $("#header").outerHeight(true));
            $("html").css("height", "calc(100% - " + $("#header").outerHeight(true) + "px)");
            
            if($(window).width() > 1200){
                $(".view-source-code").css("max-height", "calc(100vh - " + ($(".view-source-head").outerHeight(true) + $(".view-source-bottom").outerHeight(true)) + "px - 2vw - 2vw)");
                $(".img-view").css("max-height", "calc(100vh - " + ($(".view-source-head").outerHeight(true) + $(".view-source-bottom").outerHeight(true)) + "px - 2vw - 2vw)");
            
                if($(".modal").css("display") == "flex"){
                    $("#header").css("display", "flex");
                }
                
                try {
                    closeMenuMobIn();
                    $("#header").css("flex-direction", "row");
                    $(".header-logo-div").css("padding-left", "0");
                    $("#main-div-account").css("margin-top", $("#header").outerHeight(true));
                } catch (err) {
                    
                }
            }
            else{
                $(".view-source-code").css("max-height", "calc(100% - " + ($(".view-source-head").outerHeight(true) + $("#view-source-bottom-menu-button").outerHeight(true)) + "px - 0vw - 2vw - 0px)");
                $(".img-view").css("max-height", "calc(100% - " + ($(".view-source-head").outerHeight(true) + $("#view-source-bottom-menu-button").outerHeight(true)) + "px - 0vw - 2vw)");
            
                if($(".modal").css("display") == "flex"){
                    $("#header").css("display", "none");
                }
            }
        
            if($(window).width() > 1350){
                $(".source-code-buttons-div-mob-buttons").css("display", "none");
                $(".source-code-div").css("margin-bottom", "1%");
                $(".source-code-div").css("border-bottom-left-radius", "5px");
                $(".source-code-div").css("border-bottom-right-radius", "5px");

                $(".folder-div-mob-buttons").css("display", "none");
                $(".folder-div").css("margin-bottom", "1%");
                $(".folder-div").css("border-bottom-left-radius", "5px");
                $(".folder-div").css("border-bottom-right-radius", "5px");
            }

            if($("#modal-view-source").css("display") == "flex"){
                $("#header").css("display", "none");
            }

            if($(".modal .folder-info-div").css("display") == "block"){
                $(".modal-content").css("display", "none");
            }

            if($(".view-source-modal").css("display") != "none" && $(".view-source-head").attr("id") != "no-source"){
                $(".view-source-bottom").css("width", $(".view-source-head").outerWidth(true));

                if($(window).width() <= 900){
                    $(".view-source-head").css("display", "none");
                    $(".ace_editor").css("display", "none");
                    $(".view-source-bottom").css("display", "none");
                }
                else if($(window).width() <= 1300 && $(window).width() > 1200){
                    $(".view-source-head").css("width", "calc(75% - 1vw + 2px)");
                    $(".ace_editor").css("width", "calc(75% - 1vw + 2px)");
                    $(".view-source-bottom").css("width", $(".view-source-head").outerWidth(true));
                }
                else{
                    $(".view-source-head").css("display", "flex");
                    $(".ace_editor").css("display", "flex");
                    $(".view-source-bottom").css("display", "block");
                    $(".view-source-head").css("width", "calc(70% - 1vw + 2px)");
                    $(".ace_editor").css("width", "calc(70% - 1vw + 2px)");
                    $(".view-source-modal").css("left", "calc(70% - 4px)");
                    $(".view-source-bottom").css("width", $(".view-source-head").outerWidth(true));
                }
            }
        });

        $(document).on("contextmenu", function(e){
            $(".account-context-menu").css("height", "unset");

            if(!$(e.target).is("input")){
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
                        $(".account-context-menu h3:not(.file-related-cm-option)").css("display", "none");             
                        $(".file-related-cm-option").css("display", "block");
                    }
                    else{
                        $(".account-context-menu h3:not(.file-related-cm-option)").css("display", "block");
                        $(".file-related-cm-option").css("display", "none");
                    }

                    positionContextMenu(e);
                }
                else{
                    $(".account-context-menu").css("display", "none");
                }

                return false;
            }
        });

        function positionContextMenu(e){
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

            //reduces the context menu height if there isn't enough space to show it all
            if(contextMenuTop < $("#header").outerHeight(true)){
                if(contextMenuTop < 0){
                    contextMenuTop *= -1;
                }

                $(".account-context-menu").css("height", $(".account-context-menu").outerHeight(true) - contextMenuTop - $("#header").outerHeight(true));
                $(".account-context-menu").css("top", "calc(" + $(".account-context-menu").css("top") + " + " + (contextMenuTop + $("#header").outerHeight(true) + "px") + ")");
            }
        }

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

        $(document).on("dragstart", function(){
            return false;
        });
    </script>

</body>
</html>
