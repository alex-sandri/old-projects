<section class="settings-section <?php if(isset($_GET['sect']) && $_GET['sect'] === "info") echo "selected"; ?>" id="info">
    <h1>Info</h1>
    <div class="setting" id="tos-setting">
        <h2 data-translation="generic->terms_of_service"></h2>
        <div><a href="terms" data-translation="generic->terms_of_service"></a></div>
    </div>
    <div class="setting" id="pp-setting">
        <h2 data-translation="generic->privacy_policy"></h2>
        <div><a href="privacy" data-translation="generic->privacy_policy"></a></div>
    </div>
    <div class="setting" id="cp-setting">
        <h2 data-translation="generic->cookie_policy"></h2>
        <div><a href="cookie" data-translation="generic->cookie_policy"></a></div>
    </div>
</section>