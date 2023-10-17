<?php
    require(dirname(__DIR__, 3) . "/autoload.php");

    use Denvelope\Database\DatabaseInfo;
    use Denvelope\Utils\Translation;
?>

<section class="settings-section <?php if(!isset($_GET['sect']) || (isset($_GET['sect']) && $_GET['sect'] === "general")) echo "selected"; ?>" id="general">
    <h1>General</h1>
    <div class="setting" id="username-setting">
        <h2 data-translation="generic->username"></h2>
        <div>
            <p data-update-field="username"><?php echo htmlspecialchars($user[DatabaseInfo::USERS_TABLE['columns']['username']["column_name"]]); ?></p>
            <button data-translation="generic->edit"></button>
        </div>
    </div>
    <div class="setting" id="email-setting">
        <h2 data-translation="generic->email"></h2>
        <div>
            <p data-update-field="email"><?php echo htmlspecialchars($user[DatabaseInfo::USERS_TABLE['columns']['email']["column_name"]]); ?></p>
            <button data-translation="generic->edit"></button>
        </div>
    </div>
    <div class="setting" id="preferred-language-setting">
        <h2 data-translation="generic->language"></h2>
        <div>
            <p data-update-field="preferred_language"><?php echo htmlspecialchars(Translation::getLanguageName($user[DatabaseInfo::USERS_TABLE['columns']['preferred_language']["column_name"]])); ?></p>
            <button data-translation="generic->edit"></button>
        </div>
        <select name="preferred_language" id="preferred_language">
            <option value="en-US" data-selected="<?php echo var_export($user[DatabaseInfo::USERS_TABLE['columns']['preferred_language']["column_name"]] === "en-US", true); ?>">English (US)</option>
            <option value="it-IT" data-selected="<?php echo var_export($user[DatabaseInfo::USERS_TABLE['columns']['preferred_language']["column_name"]] === "it-IT", true); ?>">Italiano</option>
        </select>
    </div>
</section>