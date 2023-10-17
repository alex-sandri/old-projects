<div class="header-main-div">
    <div class="header-logo-div">
        <a href="<?php echo $urlPrefix; ?>">
            <i class="fas fa-chevron-left"></i>
            <i class="fas fa-envelope-open"></i>
            Denvelope
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
    <?php
        if((isset($betaDisableAccessHeaderLoggedOut) && !$betaDisableAccessHeaderLoggedOut) || (isset($betaDisableAccessHeader) && !$betaDisableAccessHeader)){
            echo '<div class="signup-login-header-mob" id="signup-login-header-mob">
                    <a onclick="openMenuMob()" class="menu-bars-header" id="menu-bars-header"><i class="fas fa-bars"></i></a>
                    <a onclick="closeMenuMob()" class="menu-times-header" id="menu-times-header"><i class="fas fa-times"></i></a>
                    <a onclick="backMenuMob()" class="menu-arrow-left-header" id="menu-arrow-left-header"><i class="fas fa-arrow-left"></i></a>
                </div>
            ';
        }
    ?>
</div>
<?php
    if(!isset($isSharedHeader)){
        $isSharedHeader = false;
    }
    else{
        $isSharedHeader = true;
    }

    if(((isset($betaDisableAccessHeaderLoggedOut) && !$betaDisableAccessHeaderLoggedOut) || (isset($betaDisableAccessHeader) && !$betaDisableAccessHeader)) && !$isSharedHeader && (isset($noSignupLoginFormHeader) && !$noSignupLoginFormHeader)){
        echo '<div class="signup-login-header">
                <a onclick="signUp()" class="signup-header signup-header-clicked" id="signup-header"><i class="fas fa-user-plus"></i> '; echo getTranslatedContent("header_signup"); echo'</a>
                <a onclick="logIn()" class="login-header" id="login-header"><i class="fas fa-sign-in-alt"></i> '; echo getTranslatedContent("header_login"); echo'</a>   
            </div>
            <div class="menu-mob" id="menu-mob">
                <div class="menu-items-mob">
                    <a onclick="signUp()" class="signup-header-mob" id="signup-header-mob"><i class="fas fa-user-plus"></i> '; echo getTranslatedContent("header_signup"); echo'</a>
                    <br>
                    <br>
                    <a onclick="logIn()" class="login-header-mob" id="login-header-mob"><i class="fas fa-sign-in-alt"></i> '; echo getTranslatedContent("header_login"); echo'</a>
                </div>
            </div>
        ';
    }
    else{
        echo '<div class="signup-login-header">
                <a href="/signup" class="signup-header" id="signup-header"><i class="fas fa-user-plus"></i> '; echo getTranslatedContent("header_signup"); echo'</a>
                <a href="/login" class="login-header" id="login-header"><i class="fas fa-sign-in-alt"></i> '; echo getTranslatedContent("header_login"); echo'</a>   
            </div>
            <div class="menu-mob" id="menu-mob">
                <div class="menu-items-mob">
                    <a href="/signup" class="signup-header-mob" id="signup-header-mob"><i class="fas fa-user-plus"></i> '; echo getTranslatedContent("header_signup"); echo'</a>
                    <br>
                    <br>
                    <a href="/login" class="login-header-mob" id="login-header-mob"><i class="fas fa-sign-in-alt"></i> '; echo getTranslatedContent("header_login"); echo'</a>
                </div>
            </div>
        ';
    }
?>