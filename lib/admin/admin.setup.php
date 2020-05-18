<?php if (!isset( $_GET['setupstep'] )) { ?>

    <div id="zp-Setup">

        <div id="zp-Metazot-Navigation">

            <div id="zp-Icon">
                <img src="<?php echo METAZOT_PLUGIN_URL; ?>/images/icon-64x64.png" title="Zotero + WordPress = Metazot">
            </div>

            <div class="nav">
                <div id="step-1" class="nav-item nav-tab-active"><strong>1.</strong> <?php _e('Validate Account','metazot'); ?></div>
                <div id="step-2" class="nav-item"><strong>2.</strong> <?php _e('Default Options','metazot'); ?></div>
            </div>

        </div><!-- #zp-Metazot-Navigation -->

        <div id="zp-Setup-Step">

            <?php

            $mz_check_curl = intval( function_exists('curl_version') );
            $mz_check_streams = intval( function_exists('stream_get_contents') );
            $mz_check_fsock = intval( function_exists('fsockopen') );

            if ( ($mz_check_curl + $mz_check_streams + $mz_check_fsock) <= 1 ) { ?>
            <div id="zp-Setup-Check" class="error">
                <p><strong><?php _e('Warning','metazot'); ?>!</strong> <?php _e('Metazot requires at least one of the following to work: cURL, fopen with Streams (PHP 5), or fsockopen. You will not be able to use Metazot until your administrator or tech support has set up one of these options. cURL is recommended.','metazot'); ?></p>
            </div>
            <?php } ?>

            <div id="zp-AddAccount-Form" class="visible">
                <?php include('admin.accounts.addform.php'); ?>
            </div>

        </div>

    </div>



<?php } else if (isset($_GET['setupstep']) && $_GET['setupstep'] == "two") { ?>

    <div id="zp-Setup">

        <div id="zp-Metazot-Navigation">

            <div id="zp-Icon">
                <img src="<?php echo METAZOT_PLUGIN_URL; ?>/images/icon-64x64.png" title="Zotero + WordPress = Metazot">
            </div>

            <div class="nav">
                <div id="step-1" class="nav-item"><strong>1.</strong> <?php _e('Validate Account','metazot'); ?></div>
                <div id="step-2" class="nav-item nav-tab-active"><strong>2.</strong> <?php _e('Default Options','metazot'); ?></div>
            </div>

        </div><!-- #zp-Metazot-Navigation -->

        <div id="zp-Setup-Step">

            <h3><?php _e('Set Default Options','metazot'); ?></h3>

            <?php include("admin.options.form.php"); ?>

            <div id="zp-Metazot-Setup-Buttons" class="proceed">
                <input type="button" id="zp-Metazot-Setup-Options-Complete" class="button-primary" value="<?php _e('Finish','metazot'); ?>">
            </div>

        </div>

    </div>

<?php } ?>
