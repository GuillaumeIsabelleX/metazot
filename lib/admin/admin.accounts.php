<?php

// Restrict to Editors
if ( current_user_can('edit_others_posts') )
{

	// Determine if server supports OAuth
	if (in_array ('oauth', get_loaded_extensions())) { $oauth_is_not_installed = false; } else { $oauth_is_not_installed = true; }

	if (isset( $_GET['oauth'] )) { include("admin.accounts.oauth.php"); } else {

	?>

		<div id="zp-Metazot" class="wrap">

			<?php include( dirname(__FILE__) . '/admin.menu.php' ); ?>


			<!-- METAZOT MANAGE ACCOUNTS -->

			<div id="zp-ManageAccounts">

				<h3><?php _e('Synced Zotero Accounts','metazot'); ?></h3>
				<?php if (!isset( $_GET['no_accounts'] ) || (isset( $_GET['no_accounts'] ) && $_GET['no_accounts'] != "true")) { ?><a title="<?php _e('Add Account','metazot'); ?>" class="zp-AddAccountButton button button-secondary" href="<?php echo admin_url("admin.php?page=Metazot&setup=true"); ?>"><span><?php _e('Add Account','metazot'); ?></span></a><?php } ?>

				<table id="zp-Accounts" class="wp-list-table widefat fixed posts">

					<thead>
						<tr>
							<th class="default first manage-column" scope="col"><?php _e('Default','metazot'); ?></th>
							<th class="account_type first manage-column" scope="col"><?php _e('Type','metazot'); ?></th>
							<th class="api_user_id manage-column" scope="col"><?php _e('User ID','metazot'); ?></th>
							<th class="public_key manage-column" scope="col"><?php _e('Private Key','metazot'); ?></th>
							<th class="nickname manage-column" scope="col"><?php _e('Nickname','metazot'); ?></th>
							<th class="actions last manage-column" scope="col"><?php _e('Actions','metazot'); ?></th>
						</tr>
					</thead>

					<tbody id="zp-AccountsList">
						<?php

							global $wpdb;

							$accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."metazot");
							$zebra = " alternate";

							foreach ($accounts as $num => $account)
							{
								if ($num % 2 == 0) { $zebra = " alternate"; } else { $zebra = ""; }

								$code = "<tr id='zp-Account-" . $account->api_user_id . "' class='zp-Account".$zebra."' rel='" . $account->api_user_id . "'>\n";

								// DEFAULT
								$code .= "                          <td class='default first'>";
								// if ( get_option("Metazot_DefaultAccount") && get_option("Metazot_DefaultAccount") == $account->api_user_id ) $code .= " selected";
								$code .= "<a href='javascript:void(0);' rel='". $account->api_user_id ."' class='default zp-Accounts-Default dashicons dashicons-star-";
								if ( get_option("Metazot_DefaultAccount") && get_option("Metazot_DefaultAccount") == $account->api_user_id ) $code .= "filled"; else  $code .= "empty";
								$code .= "' title='".__('Set as Default','metazot')."'>".__('Set as Default','metazot')."</a></td>\n";

								// ACCOUNT TYPE
								$code .= "                          <td class='account_type'>" . substr($account->account_type, 0, -1) . "</td>\n";

								// API USER ID
								$code .= "                          <td class='api_user_id'>" . $account->api_user_id . "</td>\n";

								// PUBLIC KEY
								$code .= "                          <td class='public_key'>";
								if ($account->public_key)
								{
									$code .= $account->public_key;
								}
								else
								{
									add_thickbox();
									$code .= 'No private key entered. <a class="zp-OAuth-Button thickbox" href="'.get_bloginfo( 'url' ).'/wp-content/plugins/metazot/lib/admin/admin.accounts.oauth.php?TB_iframe=true&width=600&height=480&oauth_user='.$account->api_user_id.'&amp;return_uri='.get_bloginfo('url').'">'.__('Start OAuth','metazot').'?</a>';
								}
								$code .= "</td>\n";

								// NICKNAME
								$code .= "                          <td class='nickname'>";
								if ($account->nickname)
									$code .= $account->nickname;
								$code .= "</td>\n";

								// ACTIONS
								$code .= "                          <td class='actions last'>\n";
								$code .= "                              <a title='".__('Clear Cache','metazot')."' class='cache dashicons dashicons-update' href='#" . $account->api_user_id . "'>".__('Clear Cache','metazot')."</a>\n";
								$code .= "                              <a title='".__('Remove','metazot')."' class='delete dashicons dashicons-trash' href='#" . $account->api_user_id . "'>".__('Remove','metazot')."</a>\n";
								$code .= "                          </td>\n";

								$code .= "                         </tr>\n\n";

								echo $code;
							}
						?>
					</tbody>

				</table>

			</div>

			<span id="METAZOT_PLUGIN_URL" style="display: none;"><?php echo METAZOT_PLUGIN_URL; ?></span>

			<?php if ( ! $oauth_is_not_installed ) { ?>
				<h3><?php _e('What is OAuth?','metazot'); ?></h3>

				<p>
					OAuth helps you create the necessary private key for allowing Metazot to read your Zotero library and display
					it for all to see. You can do this manually through the Zotero website; using OAuth in Metazot is just a quicker, more straightforward way of going about it.
					<strong>Note: You'll need to have OAuth installed on your server to use this option.</strong> If you don't have OAuth installed, you'll have to generate a private key manually through the <a href="http://www.zotero.org/">Zotero</a> website.
				</p>
			<?php } ?>


		</div>

<?php

	} /* OAuth check */

} // !current_user_can('edit_others_posts')

else
{
	echo "<p>".__("Sorry, you don't have permission to access this page.","metazot")."</p>";
}

?>
