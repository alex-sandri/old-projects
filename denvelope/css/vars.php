<?php
    header("Content-type: text/css");

    require("../php/global-vars.php");
?>

<?php
    echo "
        :root{
            --font-family: 'Ubuntu Mono', monospace;/*'Source Code Pro', monospace;*/
            --logo-font-family: 'Montserrat', sans-serif;
            --font-awesome-font-family: 'Font Awesome 5 Free';
            --body-color: "; echo $monochromaticBody ? $HEADER_COLOR : $BODY_COLOR; echo";
            --text-color: "; echo $TEXT_COLOR; echo";
            --font-weight-bold: 900;
            --font-weight-regular: 700;
            --font-weight-light: 400;
            /**/
            --signup-login-form-bgcolor: var(--body-color);
            --input-bgcolor: "; echo $INPUT_BGCOLOR; echo";
            --input-text-color: var(--header-color);
            --error-text-color: var(--text-color);
            --form-change-text-color: "; echo $FORM_CHANGE_TEXT_COLOR; ;echo";
            /**/
            --header-color: "; echo $HEADER_COLOR; echo";
            --signup-login-toggle-bgcolor: var(--header-color);
            --settings-header-link-bgclor: var(--header-color);
            /**/
            --account-settings-bgcolor: var(--body-color);
            --delete-account-button-bgcolor-hover: #D13A28;
            /**/
            --form-bg-image-gradient-color: "; echo $monochromaticBody ? $FORM_BG_IMAGE_GRADIENT_COLOR_MOB : $FORM_BG_IMAGE_GRADIENT_COLOR; echo";
            --form-bg-image-gradient-color-mob: "; echo $FORM_BG_IMAGE_GRADIENT_COLOR_MOB; echo";
        }
    ";
?>