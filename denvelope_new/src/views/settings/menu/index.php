<div class="settings-menu">
    <button data-sect="general" <?php if(!isset($_GET['sect']) || (isset($_GET['sect']) && $_GET['sect'] === "general")) echo "class=\"selected\""; ?>>
        <i class="fas fa-cog"></i>
        General
    </button>
    <button data-sect="plan" <?php if(isset($_GET['sect']) && $_GET['sect'] === "plan") echo "class=\"selected\""; ?>>
        <i class="fas fa-credit-card"></i>
        Plan
    </button>
    <button data-sect="security" <?php if(isset($_GET['sect']) && $_GET['sect'] === "security") echo "class=\"selected\""; ?>>
        <i class="fas fa-shield-alt"></i>
        Security
    </button>
    <button data-sect="advanced" <?php if(isset($_GET['sect']) && $_GET['sect'] === "advanced") echo "class=\"selected\""; ?>>
        <i class="fas fa-sliders-h"></i>
        Advanced
    </button>
    <button data-sect="info" <?php if(isset($_GET['sect']) && $_GET['sect'] === "info") echo "class=\"selected\""; ?>>
        <i class="fas fa-info-circle"></i>
        Info
    </button>
    <button data-sect="privacy" <?php if(isset($_GET['sect']) && $_GET['sect'] === "privacy") echo "class=\"selected\""; ?>>
        <i class="fas fa-user-shield"></i>
        Privacy
    </button>
</div>