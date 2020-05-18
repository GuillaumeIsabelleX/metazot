<?php

// Restrict to Editors
if ( current_user_can('edit_others_posts') )
{

?>
<!-- START OF ACCOUNT -->
				<div class="zp-Column-1">
					<div class="zp-Column-Inner">

						<h4><?php _e('Set Default Account','metazot'); ?></h4>

						<p class="note"><?php _e('Note: Only applicable if you have multiple synced Zotero accounts.','metazot'); ?></p>

						<div id="zp-Metazot-Options-Account" class="zp-Metazot-Options">

							<label for="zp-Metazot-Options-Account"><?php _e('Choose Account','metazot'); ?>:</label>
							<select id="zp-Metazot-Options-Account">
								<?php

								global $wpdb;
								$mz_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."metazot ORDER BY account_type DESC");
								$mz_accounts_total = $wpdb->num_rows;

								// See if default exists
								$mz_default_account = "";
								if (get_option("Metazot_DefaultAccount"))
									$mz_default_account = get_option("Metazot_DefaultAccount");

								foreach ($mz_accounts as $mz_account)
									if ($mz_account->api_user_id == $mz_default_account)
										echo "<option id=\"".$mz_account->api_user_id."\" value=\"".$mz_account->api_user_id."\" selected='selected'>".$mz_account->api_user_id." (".$mz_account->nickname.") [".substr($mz_account->account_type, 0, strlen($mz_account->account_type)-1)."]</option>\n";
									else
										echo "<option id=\"".$mz_account->api_user_id."\" value=\"".$mz_account->api_user_id."\">".$mz_account->api_user_id." (".$mz_account->nickname.") [".substr($mz_account->account_type, 0, strlen($mz_account->account_type)-1)."]</option>\n";
								?>
							</select>

							<input type="button" id="zp-Metazot-Options-Account-Button" class="zp-Accounts-Default button-secondary" value="<?php _e('Set Default Account','metazot'); ?>">
							<div class="zp-Loading">loading</div>
							<div class="zp-Success"><?php _e('Success','metazot'); ?>!</div>
							<div class="zp-Errors"><?php _e('Errors','metazot'); ?>!</div>

							<h4 class="clear">

						</div>
						<!-- END OF ACCOUNT -->

					</div>
				</div>

				<div class="zp-Column-2">
					<div class="zp-Column-Inner">

						<!-- START OF STYLE -->
						<h4><?php _e('Set Default Citation Style for Importing','metazot'); ?></h4>

						<p class="note"><?php
							echo sprintf(
								wp_kses(
									__( 'Note: Styles must be listed <a title="Zotero Styles" href="%s">here</a>. Use the name found in the style\'s URL, e.g. modern-language-association.', 'metazot' ),
									array(
										'a' => array(
											'href' => array()
										)
									)
								), esc_url( 'http://www.zotero.org/styles' )
							); ?>
						</p>

						<div id="zp-Metazot-Options-Style-Container" class="zp-Metazot-Options">

							<label for="zp-Metazot-Options-Style"><?php _e('Choose Style','metazot'); ?>:</label>
							<select id="zp-Metazot-Options-Style">
								<?php

								if (!get_option("Metazot_StyleList"))
									add_option( "Metazot_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nature, vancouver");

								$mz_styles = explode(", ", get_option("Metazot_StyleList"));
								sort($mz_styles);

								// See if default exists
								$mz_default_style = "apa";
								if (get_option("Metazot_DefaultStyle"))
									$mz_default_style = get_option("Metazot_DefaultStyle");

								foreach($mz_styles as $mz_style)
									if ($mz_style == $mz_default_style)
										echo "<option id=\"".$mz_style."\" value=\"".$mz_style."\" selected='selected'>".$mz_style."</option>\n";
									else
										echo "<option id=\"".$mz_style."\" value=\"".$mz_style."\">".$mz_style."</option>\n";

								?>
								<option id="new" value="new-style"><?php _e('Add another style','metazot'); ?> ...</option>
							</select>

							<div id="zp-Metazot-Options-Style-New-Container">
								<label for="zp-Metazot-Options-Style-New"><?php _e('Add Style','metazot'); ?>:</label>
								<input id="zp-Metazot-Options-Style-New" type="text">
							</div>

							<input type="button" id="zp-Metazot-Options-Style-Button" class="button-secondary" value="<?php _e('Set Default Style','metazot'); ?>">
							<div class="zp-Loading">loading</div>
							<div class="zp-Success"><?php _e('Success','metazot'); ?>!</div>
							<div class="zp-Errors"><?php _e('Errors','metazot'); ?>!</div>

							<hr class="clear">

						</div>
						<!-- END OF STYLE -->

					</div>
				</div>
<?php

} // !current_user_can('edit_others_posts')

else
{
	echo "<p>".__("Sorry, you don't have permission to access this page.",'metazot') ."</p>";
}

?>
