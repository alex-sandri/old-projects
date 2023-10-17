<header class="header" id="header">
    <?php
        if((isset($betaDisableAccessHeaderAccount) && !$betaDisableAccessHeaderAccount) || (isset($betaDisableAccessHeader) && !$betaDisableAccessHeader)){
            if(!isset($_SESSION['username'])){
                require("logged-out-header.php");
            }
            else{
                require("logged-in-header.php");
            }
        }
        else{
            require("basic-header.php");
        }
    ?>
</header>
