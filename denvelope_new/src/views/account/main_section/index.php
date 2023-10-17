<main class="account-main">
    <?php
        require(dirname(__FILE__) . "/search_bar/index.php");
    ?>
    <p class="icons8-message">
        Icons are provided by
        <a href="https://icons8.com/" target="_blank" rel="noopener">
            <img src="assets/img/icons/others/icons8.svg" aria-hidden="true">Icons8
        </a>
    </p>
    <?php  
        require(dirname(__FILE__) . "/user_contents/index.php");
        
        require(dirname(__FILE__, 3) . "/miscellaneous/modal/index.php");
    ?>
</main>