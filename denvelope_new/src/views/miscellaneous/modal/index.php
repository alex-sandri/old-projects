<?php
    require(dirname(__FILE__, 4) . "/autoload.php");

    use Denvelope\Utils\Translation;
?>

<div class="modal">
    <div name="content" class="content"><div class="heading"></div></div>
    <span class="spinner"><i class="fas fa-circle-notch"></i></span>
    <button class="confirm-button confirm" data-translation="generic->confirm"><i class="fas fa-check"></i></button>
    <button class="update-button update" data-translation="generic->update"><i class="fas fa-edit"></i></button>
    <button class="close-button close" data-translation="generic->close" aria-label="<?php echo Translation::get("account->aria_labels->modal->close"); ?>"><i class="fas fa-times"></i></button>
</div>