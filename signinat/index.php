<?php
    require(dirname(__FILE__) . "/lib/autoload.php");

    use SignInAt\Config\Config;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SignInAt</title>
    <link rel="stylesheet" href="css/min/main.min.css<?php echo "?" . bin2hex(random_bytes(8)); ?>">
    <link rel="stylesheet" href="<?php echo Config::FONTAWESOME_URL; ?>">
</head>
<body>
    <?php
        require(dirname(__FILE__) . "/components/header.php");
    ?>

    <div>
        <h1>Forget about creating a new account every time you visit a website</h1>
        <h2>SignInAt is the account to rule them all</h2>
    </div>
</body>
</html>