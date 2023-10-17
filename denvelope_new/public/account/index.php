<?php
    require(dirname(__FILE__, 3) . "/src/autoload.php");

    use Denvelope\Config\Config;

    use Denvelope\Models\File;
    use Denvelope\Models\Folder;

    use Denvelope\Utils\Translation;

    if ((isset($_GET['folder']) && !Folder::Exists(["id" => $_GET['folder']])) || isset($_GET['file']) && !File::Exists(["id" => $_GET['file']]))
    {
        header("Location: ../");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="<?php echo Translation::getCurrentLanguage(); ?>">
<head>
    <base href="../<?php if((isset($_GET['folder']) || isset($_GET['file'])) && $_SERVER['REQUEST_URI'][\mb_strlen($_SERVER['REQUEST_URI']) - 1] === "/") echo "../"; ?>">
    <?php
        echo Config::getDefaultHTMLTags();
    ?>
    <title data-translation="account->title"></title>
</head>
<body class="preload">
    <?php
        require dirname(__DIR__, 2) . "/src/views/header/index.html";

        require dirname(__DIR__, 2) . "/src/views/account/main_section/index.php";

        require dirname(__DIR__, 2) . "/src/views/account/side_menu/index.php";

        require dirname(__DIR__, 2) . "/src/views/miscellaneous/generic_message/index.php";

        require dirname(__DIR__, 2) . "/src/views/miscellaneous/firebase/index.html";

        require dirname(__DIR__, 2) . "/src/views/editor/index.php";
    ?>

    <input type="hidden" name="app-root" value="<?php echo Config::getAppRoot(); ?>">
    <input type="hidden" name="api-endpoint" value="<?php echo Config::getApiEndpoint(); ?>">
    <input type="hidden" name="folder-id" value="<?php echo Config::getFolderId(); ?>">
    <script type="module" src="assets/js/account.js<?php echo "?v=" . filemtime(dirname(__FILE__, 2) . "/assets/js/account.js") ?>"></script>
</body>
</html>