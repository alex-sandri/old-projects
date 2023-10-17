<?php
    //DENVELOPE_IGNORE

    if($_SERVER['REMOTE_ADDR'] == "::1" || $_SERVER['REMOTE_ADDR'] == "127.0.0.1"){ //a copy of this is on is-production.php
        $isProduction = false;
    }
    else{
        $isProduction = true;
    }

    $AWS_S3_BUCKET = "elasticbeanstalk-us-east-1-298288330487";

    if($isProduction){
        $AWS_ACCESS_KEY_ID = $_SERVER['AWS_ACCESS_KEY_ID'];
        $AWS_SECRET_ACCESS_KEY = $_SERVER['AWS_SECRET_ACCESS_KEY'];
    }

    if($isProduction){
        $urlPrefix = "/";
    }
    else{
        $urlPrefix = "/denvelope/"; //not using the base tag for issues with other pages this header maybe included in
    }

    $googleAnalyticsTag = '<!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-115210355-2"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag(\'js\', new Date());
    
      gtag(\'config\', \'UA-115210355-2\');
    </script>
    ';

    $hotjarTag = "<!-- Hotjar Tracking Code for https://denvelope.com -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:1460754,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
    ";

    require_once("check-lang.php");
    require_once("delete-cookie.php");
    require_once("get-user-preferred-language.php");
    require_once("create-cookie.php");

    if(!checkLang()){
        deleteCookie("lang");

        header("Location: ./");
        exit();
    }
    else{
        if(isset($_SESSION['lang'])){
            $lang = $_SESSION['lang']; //change language with session variable if the lang cookie value changed, if not page would need refresh to update
        }
        else if(isset($_COOKIE['lang'])){
            $lang = $_COOKIE['lang'];
        }
        else{
            if(isset($_SESSION['username'])){
                $lang = getUserPreferredLanguage();
            }
            else if(isset($_SESSION['langFromIP'])){
                $lang = $_SESSION['langFromIP'];

                unset($_SESSION['langFromIP']);
            }
            else{
                $lang = "en";
            }

            if(!isset($_SESSION['requiredFromFileIgnoreCookie'])){ //do not create a cookie while retrieving the country through the API, I probably need a separate file for API keys
                createCookie("lang", $lang, 60);
            }
            else{
                unset($_SESSION['requiredFromFileIgnoreCookie']);
            }
        }
    }

    require_once("hex-to-rgb.php");

    //THEME
    $monochromaticBody = true; //header and body have the same color (already handled in vars.php and on the theme switch)

    //DEFAULT COLORS
    $HEADER_COLOR = "#160C28";
    $BODY_COLOR = "#000411";
    $TEXT_COLOR = "#EFCB68";
    $FORM_CHANGE_TEXT_COLOR = "#E6AF2E";

    if(isset($_COOKIE['theme'])){
        switch($_COOKIE['theme']){
            case "autumn":
                $HEADER_COLOR = "#210203";
                $BODY_COLOR = "#C17767";
                $TEXT_COLOR = "#D3B99F";
                $FORM_CHANGE_TEXT_COLOR = "#2F2504";
                break;
            case "deep-koamaru":
                $HEADER_COLOR = "#2F3061";
                $BODY_COLOR = "#2F3061";
                $TEXT_COLOR = "#FFE66D";
                $FORM_CHANGE_TEXT_COLOR = "#E6AF2E";
                break;
            case "moonstone-blue":
                $HEADER_COLOR = "#6CA6C1";
                $BODY_COLOR = "#6CA6C1";
                $TEXT_COLOR = "#FFE66D";
                $FORM_CHANGE_TEXT_COLOR = "#E6AF2E";
                break;
            default:
                break;
        }
    }

    //ASSIGNED DYNAMICALLY BASED ON THE THEME
    $INPUT_BGCOLOR = "rgba(".hexToRGB($HEADER_COLOR).", 0.7)";
    $FORM_BG_IMAGE_GRADIENT_COLOR = "rgba(".hexToRGB($BODY_COLOR).", 0.5)";
    $FORM_BG_IMAGE_GRADIENT_COLOR_MOB = "rgba(".hexToRGB($HEADER_COLOR).", 0.5)";

    //reCAPTCHA
    $reCAPTCHASiteKey = "6LdfZ7QUAAAAAA_BwE3e5mPFZ03NChTGhE2K8PoV";
    $reCAPTCHAPrivateKey = "6LdfZ7QUAAAAAILEWISzRyCrVbL4WpbZvvhFhRPj";

    //ipstack
    $ipstackAccessKey = "b885b147270266303671457a414438f1";

    //smartip
    $smartipAPIKey = "ec4fdc55-8aa6-4266-909d-3784691265ce";

    //Authy (2FA)
    $authyAPIKey = "ewsTw6GcsKpdhKXGtTFb8Xhz0Dbw1MLl";
    $authyCountryPrefix = array(
        "en" => "1",
        "it" => "39",
    )[$lang];
    
    //Stripe
    $stripePublicAPIKey = "pk_live_sHxwyS2lmfztyoxY2bsFu0jD008PWb3p7E";
    $stripeSecretAPIKey = "sk_live_EU2Dv0YfPJ7MgmbnIIrRRipp00rqLnBiLz";

    //currency (for Stripe)
    $currency = array(
        "en" => "$",
        "it" => "€",
    )[$lang];

    //STORAGE TIERS
    $FREE_TIER_STORAGE = "100MB";
    $FREE_TIER_PRICE = array(
        "$" => "0.00",
        "€" => "0.00",
    )[$currency];
    /*
    $FREE_TIER_STRIPE_PLAN_ID = array(
        "$" => "",
        "€" => "",
    )[$currency];
    */
    $PERSONAL_TIER_STORAGE = "1GB";
    $PERSONAL_TIER_PRICE = array(
        "$" => "0.99",
        "€" => "0.99",
    )[$currency];
    $PERSONAL_TIER_STRIPE_PLAN_ID = array(
        "$" => "plan_Fm1yUJ1FEra0gG",
        "€" => "plan_Fm1yRS3kKfBf5I",
    )[$currency];
    $PERSONAL_PLUS_TIER_STORAGE = "10GB";
    $PERSONAL_PLUS_TIER_PRICE = array(
        "$" => "2.99",
        "€" => "2.99",
    )[$currency];
    $PERSONAL_PLUS_TIER_STRIPE_PLAN_ID = array(
        "$" => "plan_Fm1zO6r6adoIVT",
        "€" => "plan_Fm1zwRwMUNk8nA",
    )[$currency];
    $PROFESSIONAL_TIER_STORAGE = "25GB";
    $PROFESSIONAL_TIER_PRICE = array(
        "$" => "4.99",
        "€" => "4.99",
    )[$currency];
    $PROFESSIONAL_TIER_STRIPE_PLAN_ID = array(
        "$" => "plan_Fm20tZLz3NXtnD",
        "€" => "plan_Fm204Ex6RDNuNu",
    )[$currency];
    $PROFESSIONAL_PLUS_TIER_STORAGE = "50GB";
    $PROFESSIONAL_PLUS_TIER_PRICE = array(
        "$" => "8.99",
        "€" => "8.99",
    )[$currency];
    $PROFESSIONAL_PLUS_TIER_STRIPE_PLAN_ID = array(
        "$" => "plan_Fm20nesZYxkGC8",
        "€" => "plan_Fm219oES2MvhFK",
    )[$currency];
    $ENTERPRISE_TIER_STORAGE = "100GB";
    $ENTERPRISE_TIER_PRICE = array(
        "$" => "12.99",
        "€" => "12.99",
    )[$currency];
    $ENTERPRISE_TIER_STRIPE_PLAN_ID = array(
        "$" => "plan_Fm21vCE9lR3uqn",
        "€" => "plan_Fm22kDXgdDlnla",
    )[$currency];

    //Authy App store (Google Play and App Store) badges
    $GOOGLE_PLAY_AUTHY_APP_BADGE_DATA = array(
        "en" => [
            "img" => "/img/store-badges/google-play/google-play-badge-en.svg",
            "store-url" => "https://play.google.com/store/apps/details?id=com.authy.authy"
        ],
        "it" => [
            "img" => "/img/store-badges/google-play/google-play-badge-it.svg",
            "store-url" => "https://play.google.com/store/apps/details?id=com.authy.authy"
        ],
    )[$lang];
    $APP_STORE_AUTHY_APP_BADGE_DATA = array(
        "en" => [
            "img" => "/img/store-badges/app-store/app-store-badge-en.svg",
            "store-url" => "https://apps.apple.com/us/app/authy/id494168017",
        ],
        "it" => [
            "img" => "/img/store-badges/app-store/app-store-badge-it.svg",
            "store-url" => "https://apps.apple.com/it/app/authy/id494168017",
        ],
    )[$lang];

    $GOOGLE_PLAY_AUTHY_APP_BADGE = "<a target='_blank' href='".$GOOGLE_PLAY_AUTHY_APP_BADGE_DATA['store-url']."'><img src='".$GOOGLE_PLAY_AUTHY_APP_BADGE_DATA['img']."'></a>";
    $APP_STORE_AUTHY_APP_BADGE = "<a target='_blank' href='".$APP_STORE_AUTHY_APP_BADGE_DATA['store-url']."'><img src='".$APP_STORE_AUTHY_APP_BADGE_DATA['img']."'></a>";
?>