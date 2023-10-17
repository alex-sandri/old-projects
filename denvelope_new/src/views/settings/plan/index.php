<?php
    require(dirname(__DIR__, 3) . "/autoload.php");

    use Denvelope\Config\Config;
    use Denvelope\Utils\Translation;
    use Denvelope\Utils\Utilities;
?>

<section class="settings-section <?php if(isset($_GET['sect']) && $_GET['sect'] === "plan") echo "selected"; ?>" id="plan">
    <h1>Plan</h1>
    <div class="plan-container">
        <?php
            foreach (Config::PLANS as $plan) {
                echo "
                    <div class=\"plan\">
                        <h1>" . htmlspecialchars($plan['name']) . "</h1>
                        <p class=\"price\">" . htmlspecialchars(Utilities::formatCurrency($plan['price'][Translation::getCurrency()['id']])) . "</p>
                    </div>
                ";
            }
        ?>
    </div>
</section>