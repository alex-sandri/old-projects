<?php
    require(dirname(__FILE__, 2) . "/src/autoload.php");

    use Denvelope\Config\Config;
    use Denvelope\Models\Cookie;

    if (Cookie::exists(Config::IS_LOGGED_IN_COOKIE_NAME) && Cookie::get(Config::IS_LOGGED_IN_COOKIE_NAME) === "true")
    {
        header("Location: account");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <base href="./">
    <meta name="description" data-content-translation="home->description">
    <?php echo Config::getDefaultHTMLTags(); ?>
    <link rel="stylesheet" href="https://www.gstatic.com/firebasejs/ui/4.3.0/firebase-ui-auth.css">
    <title>Denvelope</title>
</head>
<body class="preload">
    <?php require dirname(__DIR__) . "/src/views/header/index.html"; ?>

    <div class="home-hero">
        <img src="assets/img/home/web-developer.svg" alt="">
        <div class="text">
            <h1 data-translation="home->hero->heading"></h1>
            <h2 data-translation="home->hero->subheading"></h2>
        </div>
        <div id="firebaseui-auth-container"></div>
    </div>
    <section class="home-features">
        <div class="home-feature-container">
            <img src="assets/img/home/features/privacy-protection.svg" alt="" loading="lazy">
            <div>
                <h1 data-translation="home->feature_section->privacy->title"></h1>
                <p data-translation="home->feature_section->privacy->description"></p>
            </div>
        </div>
        <div class="home-feature-container">
            <img src="assets/img/home/features/file-sync.svg" alt="" loading="lazy">
            <div>
                <h1 data-translation="home->feature_section->store->title"></h1>
                <p data-translation="home->feature_section->store->description"></p>
            </div>
        </div>
        <div class="home-feature-container">
            <img src="assets/img/home/features/source-code.svg" alt="" loading="lazy">
            <div>
                <h1 data-translation="home->feature_section->view_edit->title"></h1>
                <p data-translation="home->feature_section->view_edit->description"></p>
            </div>
        </div>
        <div class="home-feature-container">
            <img src="assets/img/home/features/share-link.svg" alt="" loading="lazy">
            <div>
                <h1 data-translation="home->feature_section->share->title"></h1>
                <p data-translation="home->feature_section->share->description"></p>
            </div>
        </div>
        <div class="home-feature-container">
            <img src="assets/img/home/features/pay-online.svg" alt="" loading="lazy">
            <div>
                <h1 data-translation="home->feature_section->pay_per_use->title"></h1>
                <p data-translation="home->feature_section->pay_per_use->description"></p>
            </div>
        </div>
    </section>

    <?php require dirname(__DIR__) . "/src/views/footer/index.html"; ?>
    <?php require dirname(__DIR__) . "/src/views/miscellaneous/firebase/index.html"; ?>

    <input type="hidden" name="app-root" value="<?php echo Config::getAppRoot(); ?>">
    <input type="hidden" name="api-endpoint" value="<?php echo Config::getApiEndpoint(); ?>">
    <script type="module" src="assets/js/home.js<?php echo "?v=" . filemtime(__DIR__ . "/assets/js/home.js") ?>"></script>
</body>
</html>