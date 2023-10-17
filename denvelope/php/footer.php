<footer class="footer">
    <div style="text-align: center;">
        Copyright <i class="far fa-copyright"></i><?php echo date("Y"); ?> 
        <p>Alex Sandri</p>
    </div>
    <?php
        if(!$betaDisableAccess && !$betaHide){
            echo '<div>
                    ' /* . '<a href="pricing"><i class="fas fa-tag"></i>'; echo getTranslatedContent("footer_pricing"); echo'<span></span></a>'*/ . '
                    <a href="contact"><i class="fas fa-envelope"></i>'; echo getTranslatedContent("footer_contact_us"); echo'<span></span></a>
                    <a href="cookies"><i class="fas fa-cookie"></i>'; echo getTranslatedContent("footer_cookies"); echo'<span></span></a>
                    <a href="privacy"><i class="fas fa-user-shield"></i>'; echo getTranslatedContent("footer_privacy"); echo'<span></span></a>
                    <a href="terms"><i class="fas fa-balance-scale"></i>'; echo getTranslatedContent("footer_terms"); echo'<span></span></a>
                    <form action="'; echo $urlPrefix; echo'change-language.php" method="post">
                        <input type="hidden" name="from-footer-change"></input>
                        <select name="language" id="language" class="language-toggle footer-language-toggle">
                            <option value="en"'; if($lang == "en") echo "selected"; echo'>English</option>
                            <option value="it"'; if($lang == "it") echo "selected"; echo'>Italiano</option>  
                        </select>
                    </form>
                </div>
            ';
        }
    ?>
</footer>

<script>
    $("#language").change(function(){
        var language = $("#language")[0][$("#language")[0].selectedIndex].value;
            
        $.ajax({
            type: "POST",
            url: "<?php echo $urlPrefix."php/change-language.php"; ?>",
            data: "language=" + language,
            dataType: "JSON",
            success: function(r){
                if(r[0]['languageUpdated'] == true){
                    location.reload();
                }
            },
            error: function(r){

            }
        });
    });
</script>