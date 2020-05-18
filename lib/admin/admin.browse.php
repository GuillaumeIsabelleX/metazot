<?php

    global $wpdb;

    $mz_accounts_total = mz_get_total_accounts( $wpdb );
    $mz_account = false;
    $api_user_id = false;

	// Display Browse page if there's at least one Zotero account synced
    if ( $mz_accounts_total > 0 )
    {
		if ( isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) )
		{
            $mz_account_temp = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot WHERE api_user_id='".$_GET['api_user_id']."'", OBJECT);

            if ( count((array)$mz_account_temp) > 0 )
            {
    			$mz_account = $mz_account_temp;
    			$api_user_id = $mz_account->api_user_id;
            }
		}
		else
		{
			if ( get_option("Metazot_DefaultAccount") )
			{
				$mz_account_temp = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot WHERE api_user_id='".get_option("Metazot_DefaultAccount")."'", OBJECT);

				if ( count((array)$mz_account_temp) > 0 )
				{
                    $mz_account = $mz_account_temp;
					$api_user_id = $mz_account->api_user_id;
				}
				else
				{
					$mz_account_temp = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot LIMIT 1");

					if ( count((array)$mz_account_temp) > 0 )
					{
                        $mz_account = $mz_account_temp;
    					$api_user_id = $mz_account->api_user_id;
					}
				}
			}
			else
			{
				$mz_account_temp = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot LIMIT 1");

                if ( ( is_array($mz_account_temp) || $mz_account_temp instanceof Countable )
                        && count((array)$mz_account_temp) > 0 )
                {
                    $mz_account = $mz_account_temp;
                    $api_user_id = $mz_account->api_user_id;
				}
			}
		}


		// Use Browse class

		$zpLib = new metazotLib;
		$zpLib->setAccount($mz_account);
		$zpLib->setType("dropdown");
		$zpLib->setAdmin(true);
		$zpLib->setShowImage(true);
	?>

    <div id="zp-Metazot" class="wrap">

        <?php include( dirname(__FILE__) . '/admin.menu.php' ); ?>

        <div id="zp-Browse-Wrapper">

            <h3><?php


			if ( $mz_accounts_total === 1 ): echo __('Your Library', 'metazot'); else: ?>

				<div id="zp-Browse-Accounts">

					<?php echo mz_get_accounts( $wpdb, true, false, false, false, $api_user_id ); ?>
				</div>

			<?php endif; ?></h3>

			<div id="zp-Browse-Account-Options">

				<?php $is_default = false; if ( get_option("Metazot_DefaultAccount") && get_option("Metazot_DefaultAccount") == $api_user_id ) { $is_default = true; } ?>
				<a href="javascript:void(0);" rel="<?php echo $api_user_id; ?>" class="zp-Browse-Account-Default zp-Accounts-Default button button-secondary dashicons <?php if ( $is_default ) { echo "dashicons-star-filled disabled"; } else { echo "dashicons-star-empty"; } ?>"><?php if ( $is_default ) { echo __('Default','metazot'); } else { echo __('Set as Default','metazot'); } ?></a>

			</div>

            <span id="METAZOT_PLUGIN_URL"><?php echo METAZOT_PLUGIN_URL; ?></span>

            <?php echo $zpLib->getLib(); ?>

        </div><!-- #zp-Browse-Wrapper -->

    </div>


<?php } else { ?>

    <div id="zp-Metazot">

        <div id="zp-Setup">

            <div id="zp-Metazot-Navigation">

                <div id="zp-Icon">
                    <img src="<?php echo METAZOT_PLUGIN_URL; ?>/images/icon-64x64.png" title="<?php _e('Zotero + WordPress = Metazot','metazot'); ?>">
                </div>

                <div class="nav">
                    <div id="step-1" class="nav-item nav-tab-active"><?php _e('System Check','metazot'); ?></div>
                </div>

            </div><!-- #zp-Metazot-Navigation -->

            <div id="zp-Setup-Step">

                <h3><?php _e('Welcome to Metazot','metazot'); ?></h3>

                <div id="zp-Setup-Check">

                    <p>
                        <?php _e('Before we get started, let\'s make sure your system can support Metazot','metazot'); ?>:
                    </p>

                    <?php

                    $mz_check_curl = intval( function_exists('curl_version') );
                    $mz_check_streams = intval( function_exists('stream_get_contents') );
                    $mz_check_fsock = intval( function_exists('fsockopen') );

                    if ( ($mz_check_curl + $mz_check_streams + $mz_check_fsock) <= 1 ) { ?>

                    <div id="zp-Setup-Check-Message" class="error">
                        <p><strong><em><?php _e('Warning','metazot'); ?>:</em></strong> <?php _e('Metazot requires at least one of the following: <strong>cURL, fopen with Streams (PHP 5), or fsockopen</strong>. You will not be able to import items until your administrator or tech support has set up one of these options. cURL is recommended.','metazot'); ?></p>
                    </div>

                    <?php } else { ?>

                    <div id="zp-Setup-Check-Message" class="updated">
                        <p><strong><em><?php _e('Hurrah','metazot'); ?>!</em></strong> <?php _e('Your system meets the requirements necessary for Metazot to communicate with Zotero from WordPress','metazot'); ?>.</p>
                    </div>

                    <p><?php _e('Sometimes systems aren\'t configured to allow communication with external websites. Let\'s check by accessing WordPress.org','metazot'); ?>:

                    <?php

                    $response = wp_remote_get( "https://wordpress.org", array( 'headers' => array("Zotero-API-Version: 2") ) );

                    if ( $response["response"]["code"] == 200 ) { ?>

                    <script>

                    jQuery(document).ready(function() {

                        jQuery("#zp-Connect-Next").removeAttr("disabled").click(function()
                        {
                            window.parent.location = "admin.php?page=Metazot&setup=true";
                            return false;
                        });

                    });

                    </script>

                    <div id="zp-Setup-Check-Message" class="updated">
                        <p><strong><em><?php _e('Great','metazot'); ?>!</em></strong> <?php _e('We successfully connected to WordPress.org','metazot'); ?>.</p>
                    </div>

                    <p><?php _e('Everything appears to check out. Let\'s continue setting up Metazot by adding your Zotero account. Click "Next."','metazot'); ?>

                    <?php } else { ?>

                    <div id="zp-Setup-Check-Message" class="error">
                        <p><strong><em><?php _e('Warning','metazot'); ?>:</em></strong> <?php _e('Metazot was not able to connect to WordPress.org','metazot'); ?>.</p>
                    </div>

                    <p><?php _e('Unfortunately, Metazot ran into an error. Here\'s what WordPress has to say','metazot'); ?>: <?php if ( is_wp_error($response) ) { echo $response->get_error_message(); } else { echo __("Sorry, but there's no details on the error",'metazot'); } ?>.</p>

                    <p><?php _e('First, try reloading. If the error recurs, your system may not be set up to run Metazot. Please contact your system administrator or website host and ask about allowing PHP scripts to access content like RSS feeds from external websites through cURL, fopen with Streams (PHP 5), or fsockopen','metazot'); ?>.</p>

                    <p><?php _e('You can still try to use Metazot, but it may not work and/or you may encounter further errors','metazot'); ?>.</p>

                    <script>

                    jQuery(document).ready(function() {

                        jQuery("#zp-Connect").removeAttr("disabled").click(function()
                        {
                            window.parent.location = "admin.php?page=Metazot&setup=true";
                            return false;
                        });

                    });

                    </script>

                    <?php }
                    } ?>

                </div>

                <div class="proceed">
                    <input id="zp-Connect-Next" name="zp-Connect" class="button-primary" type="submit" value="<?php _e('Next','metazot'); ?>" tabindex="5" disabled="disabled">
                </div>

            </div>

        </div>

    </div>

<?php } ?>
