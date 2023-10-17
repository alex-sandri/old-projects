<?php
    require(dirname(__FILE__, 4) . "/src/autoload.php");

    use Denvelope\Config\Config;

    use Denvelope\Models\UserSession;

    use Denvelope\Utils\Translation;

    if(!UserSession::isValid())
    {
        header("Location: ../../");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="<?php echo Translation::getCurrentLanguage(); ?>">
<head>
    <base href="../../<?php if(isset($_GET['sect']) && $_SERVER['REQUEST_URI'][\mb_strlen($_SERVER['REQUEST_URI']) - 1] === "/") echo "../"; ?>">
    <?php
        echo Config::getDefaultHTMLTags();
    ?>
    <title><?php echo Translation::get("generic->settings") ?></title>
</head>
<body class="preload">
    <?php
        require(dirname(__FILE__, 4) . "/src/views/header/index.html");

        require(dirname(__FILE__, 4) . "/src/views/settings/menu/index.php");

        require(dirname(__FILE__, 4) . "/src/views/settings/general/index.php");
        
        require(dirname(__FILE__, 4) . "/src/views/settings/plan/index.php");

        require(dirname(__FILE__, 4) . "/src/views/settings/security/index.php");

        require(dirname(__FILE__, 4) . "/src/views/settings/advanced/index.php");
        
        require(dirname(__FILE__, 4) . "/src/views/settings/info/index.php");

        require(dirname(__FILE__, 4) . "/src/views/settings/privacy/index.php");

        require(dirname(__FILE__, 4) . "/src/views/miscellaneous/modal/index.php");
    ?>

    <input type="hidden" name="app-root" value="<?php echo Config::getAppRoot(); ?>">
    <input type="hidden" name="settings-root" value="account/settings/">
    <input type="hidden" name="api-endpoint" value="<?php echo Config::getApiEndpoint(); ?>">
    <input type="hidden" name="folder-id" value="<?php echo Config::getFolderId(); ?>">
    <script type="module" src="assets/js/settings.js<?php echo "?v=" . filemtime(dirname(__FILE__, 3) . "/assets/js/settings.js") ?>"></script>
</body>
</html>