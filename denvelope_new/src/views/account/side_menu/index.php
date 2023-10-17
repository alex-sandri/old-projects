<?php

require(dirname(__FILE__, 4) . "/autoload.php");

use Denvelope\Config\Config;
    
use Denvelope\Utils\Translation;
use Denvelope\Utils\Utilities;

?>

<aside class="account-side-menu">
    <div class="buttons-container">
        <button id="add-files">
            <i class="fas fa-file fa-fw"></i>
            <p data-translation="account->side_menu->buttons->add_files"></p>
        </button>
        <button id="add-folder">
            <i class="fas fa-folder fa-fw"></i>
            <p data-translation="account->side_menu->buttons->add_folder"></p>
        </button>
        <button id="create-file">
            <i class="fas fa-file-code fa-fw"></i>
            <p data-translation="account->side_menu->buttons->create_file"></p>
        </button>
        <button id="create-folder">
            <i class="fas fa-folder-plus fa-fw"></i>
            <p data-translation="account->side_menu->buttons->create_folder"></p>
        </button>
    </div>
    <div class="input-container">
        <input type="file" id="files" name="files" multiple>
        <input type="file" id="folder" name="folder" webkitdirectory mozdirectory msdirectory odirectory directory>
        <div class="input" id="create-file-input">
            <input type="text" name="file-name" placeholder="File Name">
        </div>
        <div class="input" id="create-folder-input">
            <input type="text" name="folder-name" placeholder="Folder Name">
        </div>
    </div>
    <div class="storage">
        <h2 data-translation="account->side_menu->storage->title"></h2>
        <p>
            <span data-update-field="storage_used"><?php require dirname(__DIR__, 2) . "/miscellaneous/spinner/index.php" ?></span>
            <span>/</span>
            <span><?php echo Utilities::formatStorage(Config::PLANS['free']['storage'], 1000, 0) ?></span>
        </p>
        <?php
            if (true)
            {
                echo "<a href=\"account/settings/plan\" data-translation=\"account->side_menu->storage->upgrade\"><i class=\"fas fa-level-up-alt fa-fw\"></i></a>";
            }
        ?>
    </div>
    <!--
        NOT YET READY

        <div class="features">
            <button><i class="fas fa-trash"></i> Trash</button>
            <button><i class="fas fa-star"></i> Starred</button>
            <button><i class="fas fa-share-alt"></i> Shared</button>
        </div>
    -->
</aside>

<button 
    class="account-side-menu-toggle-button"
    data-hide="<?php echo Translation::get("account->aria_labels->side_menu_toggle->hide"); ?>"
    data-show="<?php echo Translation::get("account->aria_labels->side_menu_toggle->show"); ?>"
    aria-label=""
>
    <i class="fas fa-times fa-fw" data-menu-icon-use="hide"></i>
    <i class="fas fa-bars fa-fw" data-menu-icon-use="show"></i>
</button>