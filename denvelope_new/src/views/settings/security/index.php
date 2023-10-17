<?php
    require(dirname(__DIR__, 3) . "/autoload.php");
?>

<section class="settings-section <?php if(isset($_GET['sect']) && $_GET['sect'] === "security") echo "selected"; ?>" id="security">
    <h1>Security</h1>
    <div class="setting" id="change-password-setting">
        <h2>Change Password</h2>
        <div>
            <button>Change</button>
        </div>
    </div>
</section>