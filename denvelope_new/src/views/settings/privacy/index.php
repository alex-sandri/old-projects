<?php
    require(dirname(__DIR__, 3) . "/autoload.php");

    use Denvelope\Database\DatabaseInfo;
    use Denvelope\Utils\Translation;
?>

<section class="settings-section <?php if(isset($_GET['sect']) && $_GET['sect'] === "privacy") echo "selected"; ?>" id="privacy">
    <div class="privacy-message">
        <p>We are for a private web, so we literally do not collect anything about our users</p>
    </div>
    <h1>Privacy</h1>
    <h2>This sections is as empty as our database logs about you</h2>
</section>