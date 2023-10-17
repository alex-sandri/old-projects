<?php
    require(dirname(__DIR__, 3) . "/autoload.php");

    use Denvelope\Utils\Translation;
?>

<div class="pwa-banner">
    <button class="dismiss-banner">
        <i class="fas fa-times"></i>
    </button>
    <p><?php echo Translation::get("pwa_banner->text"); ?></p>
    <button class="install-pwa">
        <?php echo Translation::get("pwa_banner->install"); ?>
    </button>
</div>