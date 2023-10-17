<?php
    require("is-production.php");

    if($isProduction){
        $prefix = "/";
    }
    else{
        $prefix = "/denvelope/";
    }
?>

<div class="header-main-div-in">
    <div class="header-logo-div">
        <a href="<?php echo $prefix; ?>account">
            <i class="fas fa-chevron-left"></i>
            <i class="fas fa-envelope-open"></i>
            Denvelope
            <i class="fas fa-chevron-right"></i>
        </a>
        <span style="font-size: 12px; font-weight: var(--font-weight-light);">BETA</span>
    </div>
    <div class="account-logout-header-mob" id="account-logout-header-mob">
        <a onclick="openMenuMobIn()" class="menu-bars-header" id="menu-bars-header">
            <i class="fas fa-bars"></i>
        </a>
        <a onclick="closeMenuMobIn()" class="menu-times-header" id="menu-times-header"><i class="fas fa-times"></i></a>
    </div>
</div>
<div class="account-logout-header">
    <div class="account-header">
        <a href="<?php echo $prefix; ?>account/settings" class="account-header-a-settings">
            <i class="fas fa-cog"></i>
            <?php echo getTranslatedContent("header_settings"); ?>
        </a>
        <a href="<?php echo $prefix; ?>account" class="account-header-a">
            <i class="fas fa-user"></i>
            <?php echo $_SESSION['username']; ?>
        </a>
    </div>
    <a href="<?php echo $prefix; ?>logout" class="logout-header">
        <i class="fas fa-sign-out-alt"></i>
        <?php echo getTranslatedContent("header_logout"); ?>
    </a>
</div>
<div class="menu-mob" id="menu-mob">
    <div class="menu-items-mob">
        <a href="<?php echo $prefix; ?>account/settings" class="account-header-a-settings-mob">
            <i class="fas fa-cog"></i>
            <?php echo getTranslatedContent("header_settings"); ?>
        </a>
        <br>
        <br>
        <a href="<?php echo $prefix; ?>account" class="account-header-a-mob">
            <i class="fas fa-user"></i>
            <?php echo $_SESSION['username']; ?>
        </a>
        <br>
        <br>
        <a href="<?php echo $prefix; ?>logout" class="logout-header-mob">
            <i class="fas fa-sign-out-alt"></i>
            <?php echo getTranslatedContent("header_logout"); ?>
        </a>
    </div>
</div>

<script>
    $(document).on("dragstart", function(){
        return false;
    });
</script>