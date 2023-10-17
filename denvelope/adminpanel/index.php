<?php
    session_start();

    $enableIPLocation = true;

    if($enableIPLocation){
        if(!isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
            require("../php/translate-from-location.php");
        }
    }

    require("../php/dbh.php");
    require("../php/get-current-user.php");
    require("../php/global-vars.php");
    require_once("../php/create-cookie.php");

    if(isset($_COOKIE['userSession'])){
        $sessionID = $_COOKIE['userSession'];

        if(!ctype_xdigit($sessionID)){
            $cookieError = "notValidCookie";

            require("../php/delete-cookie.php");
            deleteCookie("userSession");

            header("Location: ../");
            exit();
        }

        $sqlQuery = "SELECT * FROM sessions WHERE sessionID=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
            $sqlError = "sqlError";

            header("Location: ../");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $sessionID);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if($row){
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];

                $sessionID = bin2hex(random_bytes(64));
                            
                $sqlQuery = "UPDATE sessions SET sessionID=? WHERE (username=? OR email=?) AND sessionID=?";
                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $sqlQuery)){
                    $sqlError = "sqlError";

                    header("Location: ../");
                    exit();
                }
                else{
                    mysqli_stmt_bind_param($stmt, "ssss", $sessionID, $_SESSION['username'], $_SESSION['email'], $_COOKIE['userSession']);
                    mysqli_stmt_execute($stmt);
                                
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];

                    createCookie("userSession", $sessionID);

                    mysqli_stmt_close($stmt);
                }
            }
            else{
                header("Location: ../php/logout.php?ref=adminpanel");
                exit();
            }
        }
    }

    if(!isset($_SESSION['username'])){
        header("Location: ../login/?ref=adminpanel");
        exit();
    }

    $user = getUser();

    if($user['accountType'] != "admin"){
        header("Location: ../");
        exit();
    }
?>

<?php
    if(isset($_COOKIE['userSession'])){
        require("../php/update-last-activity.php");

        updateLastActivity($sessionID);
    }
?>

<?php
    require("../lang/".$lang.".php");
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <?php
        if($isProduction){
            echo $googleAnalyticsTag;
        }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="<?php echo $HEADER_COLOR; ?>">
    <meta name="msapplication-navbutton-color" content="<?php echo $HEADER_COLOR; ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?php echo getTranslatedContent("admin_panel_title"); ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <link rel="stylesheet" href="../css/signup-login-pages.css">
    <link rel="stylesheet" href="../css/admin-panel.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/pace.js"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
</head>
<body style="overflow: unset;">
    <?php
        $betaDisableAccessHeader = false;

        require("../php/header.php");
    ?>

    <div class="admin-panel-main-div" id="admin-panel-main-div">
        <h2 class="signup-form-h2"><?php echo getTranslatedContent("admin_panel_title") ?></h2>
        <?php
            if(isset($_GET['table'])){
                echo '<br><button class="back-button-admin-panel" id="back-button-admin-panel"><i class="fas fa-arrow-left"></i> Back</button>';
            }
        ?>
        <br>
        <div class="execute-query-div">
            <input type="text" name="query" id="query" class="input query-input" placeholder="Query">
            <button type="submit" id="query-execute-button" class="query-execute-button"><?php echo getTranslatedContent("admin_panel_query_execute") ?></button>
        </div>
        <?php
            if(!isset($_GET['table'])){
                require("admin-panel-functions/get-tables.php");

                $tables = getTables();

                echo '<div style="margin-top: 2vw;">';

                foreach ($tables as $table) {
                    echo '<a href="./?table='; echo reset($table); echo'" class="table-link">'; echo reset($table); echo'</a><br>';
                }

                echo "</div>";
            }
            else{
                require("admin-panel-functions/get-rows.php");
                require("admin-panel-functions/get-columns.php");

                $columns = getColumns($_GET['table']);

                echo "<table>";

                echo "<tr>";

                foreach ($columns as $column) {
                    echo "<th>"; echo $column['Field']; echo"</th>";
                }

                echo "</tr>";

                $rows = getRows($_GET['table']);

                foreach ($rows as $row) {
                    echo "<tr>";
                    
                    foreach ($row as $value) {
                        echo "<td>";
                        echo htmlspecialchars($value);
                        echo "</td>";
                    }

                    echo "</tr>";
                }

                echo "</table>";
            }
        ?>
    </div>

    <script>
        $("#back-button-admin-panel").click(function(){
            window.location.href = "./";
        });
    
        $("#admin-panel-main-div").css("margin-top", $("#header").outerHeight(true));

        $(window).on("load", function(){
            $("#admin-panel-main-div").css("margin-top", $("#header").outerHeight(true));
        });

        $(window).resize(function(){
            $("#admin-panel-main-div").css("margin-top", $("#header").outerHeight(true));
        });

        <?php
            if(isset($_GET['table'])){
                echo "//enables horizontal scrolling on the table with mouse
                    const slider = document.querySelector('table');
                    let isDown = false;
                    let startX;
                    let scrollLeft;
            
                    slider.addEventListener('mousedown', (e) => {
                        isDown = true;
                        slider.classList.add('active');
                        startX = e.pageX - slider.offsetLeft;
                        scrollLeft = slider.scrollLeft;
                    });
                    slider.addEventListener('mouseleave', () => {
                        isDown = false;
                        slider.classList.remove('active');
                    });
                    slider.addEventListener('mouseup', () => {
                        isDown = false;
                        slider.classList.remove('active');
                    });
                    slider.addEventListener('mousemove', (e) => {
                        if(!isDown) return;
                        e.preventDefault();
                        const x = e.pageX - slider.offsetLeft;
                        const walk = (x - startX) * 3; //scroll-fast
                        slider.scrollLeft = scrollLeft - walk;
                    });
                ";
            }
        ?>
    </script>
</body>
</html>