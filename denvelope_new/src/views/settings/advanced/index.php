<?php
    require(dirname(__DIR__, 3) . "/autoload.php");

    use Denvelope\Config\Config;
    use Denvelope\Database\DatabaseInfo;
    use Denvelope\Utils\Translation;
    use Denvelope\Utils\Utilities;
?>

<section class="settings-section <?php if(isset($_GET['sect']) && $_GET['sect'] === "advanced") echo "selected"; ?>" id="advanced">
    <h1>Advanced</h1>
    <div class="setting" id="delete-account-setting">
        <h2>Delete Account</h2>
        <div>
            <button class="delete delete-account-button">
                Delete
            </button>
        </div>
        <button class="confirm delete-account-button">
            Confirm
        </button>
    </div>
</section>