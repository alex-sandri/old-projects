<?php
    session_start();

    $enableIPLocation = true;

    if($enableIPLocation){
        if(!isset($_COOKIE['lang']) && !isset($_SESSION['lang'])){
            require("../php/translate-from-location.php");
        }
    }

    require("../php/global-vars.php");

    if(isset($_COOKIE['userSession'])){
        require("../php/update-last-activity.php");

        updateLastActivity($_COOKIE['userSession']);
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
    <title><?php echo getTranslatedContent("terms_of_service_title") ?> - Denvelope</title>
    <link rel="shortcut icon" href="<?php echo $urlPrefix; ?>img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/terms-privacy.css">
    <link rel="stylesheet" href="../css/signup-login-form.css">
    <script src="https://kit.fontawesome.com/0271e9d7a5.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../js/pace.js"></script>
    <script src="../js/signup-login-toggle.js"></script>
    <script src="../js/forgot-password.js"></script>
</head>
<body>

    <?php
        if(!isset($_COOKIE['consent'])){
            require("../php/cookie-banner.php");
        }
    ?>

    <?php
        $betaDisableAccessHeader = false;
        $noSignupLoginFormHeader = true;

        require("../php/header.php");
    ?>

    <div id="terms">
        <h2>Denvelope Terms of Service</h2>
        <h3>. Terms</h3>
        <p>By accessing the website at <a href="https://denvelope.com">https://denvelope.com</a>, you are agreeing to be bound by these terms of service, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site. The materials contained in this website are protected by applicable copyright and trademark law.</p>
        <h3>. Disclaimer</h3>
        <ol type="a">
            <li>The materials on Denvelope's website are provided on an 'as is' basis. Denvelope makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</li>
            <li>Further, Denvelope does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its website or otherwise relating to such materials or on any sites linked to this site.</li>
        </ol>
        <h3>. Limitations</h3>
        <p>In no event shall Denvelope or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Denvelope's website, even if Denvelope or a Denvelope authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.</p>
        <h3>. Accuracy of materials</h3>
        <p>The materials appearing on Denvelope's website could include technical, typographical, or photographic errors. Denvelope does not warrant that any of the materials on its website are accurate, complete or current. Denvelope may make changes to the materials contained on its website at any time without notice. However Denvelope does not make any commitment to update the materials.</p>
        <h3>. Logs</h3>
        <p>We keep logs for every major activity you do on our website, these will help us understand your use of our Services and possible issues that could happen.</p>
        <p>You can choose to Opt-Out from Log collection by checking the relative box on the Privacy Settings</p>
        <h3>. Paid Accounts</h3>
        <ol type="a">
            <li>You can increase your storage space (turning your account into a "Paid Account"). We’ll automatically bill you from the date you convert to a Paid Account and on each periodic renewal until cancellation. You’re responsible for all applicable taxes, and we’ll charge tax when required to do so. Some countries have mandatory local laws regarding your cancellation rights, and this paragraph doesn’t override these laws.</li>
            <li>You may cancel your Denvelope Paid Account at any time. Refunds are only issued if required by law. For example, users living in the European Union have the right to cancel their Paid Account subscriptions within 14 days of signing up for, upgrading to, or renewing a Paid Account.</li>
            <li>Your Paid Account will remain in effect until it's cancelled or terminated under these Terms. If you don’t pay for your Paid Account on time, we reserve the right to suspend it, remove all of its content or reduce your storage to free space levels.</li>
            <li>Your Paid Account can be downgraded at any time. If your used storage space is greater than that offered by the new tier we reserve the right to remove all the content of your account.</li>
            <li>We may change the fees in effect but will give you advance notice of these changes via a message to the email address associated with your account.</li>
        </ol>
        <h3>. Termination</h3>
        <p>You’re free to stop using our Services at any time.</p>
        <p>We reserve the right to terminate your access to the Services at any time, without notice.</p>
        <h3>. Discontinuation of Services</h3>
        <p>We may decide to discontinue the Services in response to unforeseen circumstances beyond Denvelope's control or to comply with a legal requirement. If we do so, we’ll give you reasonable prior notice so that you can export Your Stuff from our systems. If we discontinue Services in this way before the end of any fixed or minimum term you have paid us for, we do not guarantee any refunds.</p>    
        <h3>. Links</h3>
        <p>Denvelope has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by Denvelope of the site. Use of any such linked website is at the user's own risk.</p>
        <h3>. Modifications</h3>
        <p>Denvelope may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.</p>
        <h3>. Governing Law</h3>
        <p>These terms and conditions are governed by and construed in accordance with the laws of Italy and you irrevocably submit to the exclusive jurisdiction of the courts in that State or location.</p>
    </div>

    <script>
        $("#terms").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");

        $(window).on("load", function(){
            $("#terms").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");
        });

        $(window).resize(function(){
            if($("#menu-mob").css("display") == "none"){
                $("#terms").css("margin-top", "calc(" + $("#header").outerHeight(true) + "px + 5vw)");
            }
        });
    </script>
</body>
</html>