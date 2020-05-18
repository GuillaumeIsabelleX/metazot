<?php global $wpdb; ?>


<!-- START OF METAZOT METABOX -------------------------------------------------------------------------->

<div id="zp-MetazotMetaBox">

	<?php

	if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."metazot;") > 1)
	{
		// See if default exists
		$mz_default_account = false;
		if (get_option("Metazot_DefaultAccount")) $mz_default_account = get_option("Metazot_DefaultAccount");

		if ($mz_default_account !== false)
		{
			$mz_account = $wpdb->get_results(
				$wpdb->prepare(
					"
					SELECT api_user_id, nickname FROM ".$wpdb->prefix."metazot
					WHERE api_user_id = %s
					",
					$mz_default_account
				)
			);
		}
		else
		{
			$mz_account = $wpdb->get_results(
				"
				SELECT api_user_id, nickname FROM ".$wpdb->prefix."metazot LIMIT 1;
				"
			);
		}

		if (is_null($mz_account[0]->nickname) === false && $mz_account[0]->nickname != "")
			$mz_default_account = $mz_account[0]->nickname . " (" . $mz_account[0]->api_user_id . ")";
	?>
	<!-- START OF ACCOUNT -->
	<div id="zp-MetazotMetaBox-Account" rel="<?php echo $mz_account[0]->api_user_id; ?>">

		<div class="components-base-control">
	        <label class="components-base-control__label" for="zp-MetazotMetaBox-Acccount-Select">
	            <?php _e('Searching','metazot'); ?>:
	        </label>

	        <select id="zp-MetazotMetaBox-Acccount-Select" name="zp-MetazotMetaBox-Acccount-Select"><?php

	            $accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."metazot");

	            foreach ($accounts as $num => $account)
	            {
	                $account_meta = array(
	                    'id' => $account->id,
	                    'api_user_id' => $account->api_user_id,
	                    'account_type' => $account->account_type
	                );

	                echo '<option value="'.$account->api_user_id.'"';
	                if ( $mz_account[0]->api_user_id == $account->api_user_id )
	                    echo ' selected="selected"';
					echo '>';
	                if (is_null($account->nickname) === false && $account->nickname != "")
	                    echo $account->nickname . " - ";
	                echo $account->api_user_id.'</option>';
	                echo "\n";
	            }
	        ?></select>
		</div><!-- .components-base-control -->
	</div>
	<!-- END OF ACCOUNT -->
	<?php } ?>


	<!-- START OF SEARCH -->
	<div id="zp-MetazotMetaBox-Search">
		<div id="zp-MetazotMetaBox-Search-Inner">
			<input id="zp-MetazotMetaBox-Search-Input" class="help" type="text" value="<?php _e('Type to search','metazot'); ?>">
			<input type="hidden" id="METAZOT_PLUGIN_URL" name="METAZOT_PLUGIN_URL" value="<?php echo METAZOT_PLUGIN_URL; ?>">
		</div>
	</div>

	<div id="zp-MetazotMetaBox-List">
		<div id="zp-MetazotMetaBox-List-Inner"></div>
		<hr class="clear">
	</div>
	<!-- END OF SEARCH -->


	<div id="zp-MetazotMetaBox-Type" class="zp-MetazotMetaBox-Sub">
		<h4><?php _e('Type','metazot'); ?>:</h4>
		<ul class="ui-widget-header ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-corner-all">
			<li class="ui-tabs-active ui-state-active ui-state-default ui-corner-top"><a href="#zp-MetazotMetaBox-Bibliography"><?php _e('Bibliography','metazot'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="#zp-MetazotMetaBox-InText"><?php _e('In-Text','metazot'); ?></a></li>
		</ul>
    </div>



    <!-- START OF METAZOT BIBLIOGRAPHY ------------------------------------------------------------------>
    <!-- NEXT: datatype [items, tags, collections], SEARCH items, tags, collections LIMIT -------------- -->

    <div id="zp-MetazotMetaBox-Bibliography" class="ui-tabs-panel ui-widget-content ui-corner-bottom">

        <!-- START OF OPTIONS -->
        <div id="zp-MetazotMetaBox-Biblio-Options" class="zp-MetazotMetaBox-Sub">

            <h4><?php _e('Options','metazot'); ?>: <span class='toggle'><span class='toggle-button dashicons dashicons-arrow-down-alt2'></span></span></h4>

            <div id="zp-MetazotMetaBox-Biblio-Options-Inner">

                <label for="zp-MetazotMetaBox-Biblio-Options-Author"><?php _e('Filter by Author','metazot'); ?>:</label>
                <input type="text" id="zp-MetazotMetaBox-Biblio-Options-Author" value="">

                <hr>

                <label for="zp-MetazotMetaBox-Biblio-Options-Year"><?php _e('Filter by Year','metazot'); ?>:</label>
                <input type="text" id="zp-MetazotMetaBox-Biblio-Options-Year" value="">

                <hr>

                <label for="zp-MetazotMetaBox-Biblio-Options-Style"><?php _e('Style','metazot'); ?>:</label>
                <select id="zp-MetazotMetaBox-Biblio-Options-Style">
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
                            echo "<option id=\"".$mz_style."\" value=\"".$mz_style."\" rel='default' selected='selected'>".$mz_style."</option>\n";
                        else
                            echo "<option id=\"".$mz_style."\" value=\"".$mz_style."\">".$mz_style."</option>\n";

                    ?>
                </select>
                <p class="note"><?php _e('Add more styles','metazot'); ?> <a href="<?php echo admin_url( 'admin.php?page=Metazot&options=true'); ?>"><?php _e('here','metazot'); ?></a>.</p>

                <hr>

				<div class="zp-MetazotMetaBox-Field">
	                <label for="zp-MetazotMetaBox-Biblio-Options-SortBy"><?php _e('Sort By','metazot'); ?>:</label>
	                <select id="zp-MetazotMetaBox-Biblio-Options-SortBy">
	                    <option id="zp-bib-default" value="default" rel="default" selected="selected"><?php _e('Default','metazot'); ?></option>
	                    <option id="zp-bib-author" value="author"><?php _e('Author','metazot'); ?></option>
	                    <option id="zp-bib-date" value="date"><?php _e('Date','metazot'); ?></option>
	                    <option id="zp-bib-title" value="title"><?php _e('Title','metazot'); ?></option>
	                </select>
				</div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Sort Order','metazot'); ?>:
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-Biblio-Options-Sort-ASC"><?php _e('Asc','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Sort-ASC" name="sort" value="ASC" checked="checked">

                        <label for="zp-MetazotMetaBox-Biblio-Options-Sort-DESC"><?php _e('Desc','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Sort-No" name="sort" value="DESC">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Images','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-Biblio-Options-Image-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Image-Yes" name="images" value="yes">

                        <label for="zp-MetazotMetaBox-Biblio-Options-Image-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Image-No" name="images" value="no" checked="checked">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Title by Year','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-Biblio-Options-Title-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Title-Yes" name="title" value="yes">

                        <label for="zp-MetazotMetaBox-Biblio-Options-Title-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Title-No" name="title" value="no" checked="checked">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Downloadable','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-Biblio-Options-Download-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Download-Yes" name="download" value="yes">

                        <label for="zp-MetazotMetaBox-Biblio-Options-Download-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Download-No" name="download" value="no" checked="checked">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Abstract','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-Biblio-Options-Abstract-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Abstract-Yes" name="abstract" value="yes">

                        <label for="zp-MetazotMetaBox-Biblio-Options-Abstract-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Abstract-No" name="abstract" value="no" checked="checked">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Notes','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-Biblio-Options-Notes-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Notes-Yes" name="notes" value="yes">

                        <label for="zp-MetazotMetaBox-Biblio-Options-Notes-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Notes-No" name="notes" value="no" checked="checked">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Cite with RIS','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-Biblio-Options-Cite-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Cite-Yes" name="cite" value="yes">

                        <label for="zp-MetazotMetaBox-Biblio-Options-Cite-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-Biblio-Options-Cite-No" name="cite" value="no" checked="checked">
                    </div>
                </div>

                <hr>

				<div class="zp-MetazotMetaBox-Field">
	                <label for="zp-MetazotMetaBox-Biblio-Options-Limit"><?php _e('Limit By','metazot'); ?>:</label>
	                <input type="text" id="zp-MetazotMetaBox-Biblio-Options-Limit" size="4">
				</div>

            </div>
        </div>
        <!-- END OF OPTIONS -->

		<!-- START OF SHORTCODE -->
		<div id="zp-MetazotMetaBox-Biblio-Generate">

			<a id="zp-MetazotMetaBox-Biblio-Generate-Button" class="button-primary" href="javascript:void(0);"><?php _e('Generate Shortcode','metazot'); ?></a>
			<a id="zp-MetazotMetaBox-Biblio-Clear-Button" class="button" href="javascript:void(0);"><?php _e('Clear','metazot'); ?></a>

			<hr class="clear">

			<div id="zp-MetazotMetaBox-Biblio-Generate-Inner">
				<label for="zp-MetazotMetaBox-Biblio-Generate-Text"><?php _e('Shortcode','metazot'); ?>:</span></label>
				<textarea id="zp-MetazotMetaBox-Biblio-Generate-Text">[metazot]</textarea>
			</div>
		</div>
		<!-- END OF SHORTCODE -->

    </div><!-- #zp-MetazotMetaBox-Bibliography -->

    <!-- END OF METAZOT BIBLIOGRAPHY --------------------------------------------------------------------->



    <!-- START OF METAZOT IN-TEXT ------------------------------------------------------------------------->

    <div id="zp-MetazotMetaBox-InText" class="ui-tabs-panel ui-widget-content ui-corner-bottom">

        <!-- START OF OPTIONS -->
        <div id="zp-MetazotMetaBox-InText-Options" class="zp-MetazotMetaBox-Sub">

            <h4><?php _e('Options','metazot'); ?>: <span class='toggle'><span class='toggle-button dashicons dashicons-arrow-down-alt2'></span></span></h4>

            <div id="zp-MetazotMetaBox-InText-Options-Inner">

                <h5 class="first"><?php _e('In-Text','metazot'); ?> <?php _e('Options','metazot'); ?></h3>

                <label for="zp-MetazotMetaBox-InText-Options-Format"><?php _e('Format','metazot'); ?>:</label>
                <input type="text" id="zp-MetazotMetaBox-InText-Options-Format" value="(%a%, %d%, %p%)">
                <p class="note"><?php _e('Use these placeholders: %a% for author, %d% for date, %p% for page, %num% for list number.','metazot'); ?></p>

                <hr>

				<div class="zp-MetazotMetaBox-Field">
	                <label for="zp-MetazotMetaBox-InText-Options-Etal"><?php _e('Et al','metazot'); ?>:</label>
	                <select id="zp-MetazotMetaBox-InText-Options-Etal">
	                    <option id="default" value="default" selected="selected"><?php _e('Default','metazot'); ?></option>
	                    <option id="yes" value="Yes"><?php _e('Yes','metazot'); ?></option>
	                    <option id="no" value="no"><?php _e('No','metazot'); ?></option>
	                </select>
				</div>

                <hr>

				<div class="zp-MetazotMetaBox-Field">
	                <label for="zp-MetazotMetaBox-InText-Options-Separator"><?php _e('Separator','metazot'); ?>:</label>
	                <select id="zp-MetazotMetaBox-InText-Options-Separator">
	                    <option id="semicolon" value="default" selected="selected"><?php _e('Semicolon','metazot'); ?></option>
	                    <option id="default" value="comma"><?php _e('Comma','metazot'); ?></option>
	                </select>
				</div>

                <hr>

				<div class="zp-MetazotMetaBox-Field">
	                <label for="zp-MetazotMetaBox-InText-Options-And"><?php _e('And','metazot'); ?>:</label>
	                <select id="zp-MetazotMetaBox-InText-Options-And">
	                    <option id="default" value="default" selected="selected"><?php _e('No','metazot'); ?></option>
	                    <option id="and" value="and"><?php _e('and','metazot'); ?></option>
	                    <option id="comma-and" value="comma-and"><?php _e(', and','metazot'); ?></option>
	                </select>
				</div>

                <h5><?php _e('Bibliography','metazot'); ?> <?php _e('Options','metazot'); ?></h3>

                <label for="zp-MetazotMetaBox-InText-Options-Style"><?php _e('Style','metazot'); ?>:</label>
                <select id="zp-MetazotMetaBox-InText-Options-Style">
                    <?php

                    if (!get_option("Metazot_StyleList"))
                        add_option( "Metazot_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nlm, nature, vancouver");

                    $mz_styles = explode(", ", get_option("Metazot_StyleList"));
                    sort($mz_styles);

                    // See if default exists
                    $mz_default_style = "apa";
                    if (get_option("Metazot_DefaultStyle")) $mz_default_style = get_option("Metazot_DefaultStyle");

                    foreach($mz_styles as $mz_style)
                        if ($mz_style == $mz_default_style)
                            echo "<option id=\"".$mz_style."\" value=\"".$mz_style."\" rel='default' selected='selected'>".$mz_style."</option>\n";
                        else
                            echo "<option id=\"".$mz_style."\" value=\"".$mz_style."\">".$mz_style."</option>\n";

                    ?>
                </select>
                <p class="note"><?php _e('Add more styles','metazot'); ?> <a href="<?php echo admin_url( 'admin.php?page=Metazot&options=true'); ?>"><?php _e('here','metazot'); ?></a>.</p>

                <hr>

                <!--Sort by:-->
				<div class="zp-MetazotMetaBox-Field">
	                <label for="zp-MetazotMetaBox-InText-Options-SortBy"><?php _e('Sort By','metazot'); ?>:</label>
	                <select id="zp-MetazotMetaBox-InText-Options-SortBy">
	                    <option id="default" value="default" rel="default" selected="selected"><?php _e('Default','metazot'); ?></option>
	                    <option id="author" value="author"><?php _e('Author','metazot'); ?></option>
	                    <option id="date" value="date"><?php _e('Date','metazot'); ?></option>
	                    <option id="title" value="title"><?php _e('Title','metazot'); ?></option>
	                </select>
				</div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Sort Order','metazot'); ?>:
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-InText-Options-Sort-ASC"><?php _e('Asc','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Sort-ASC" name="sort" value="ASC" checked="checked">

                        <label for="zp-MetazotMetaBox-InText-Options-Sort-DESC"><?php _e('Desc','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Sort-No" name="sort" value="DESC">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Images','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-InText-Options-Image-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Image-Yes" name="images" value="yes">

                        <label for="zp-MetazotMetaBox-InText-Options-Image-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Image-No" name="images" value="no" checked="checked">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Title by Year','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-InText-Options-Title-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Title-Yes" name="title" value="yes">

                        <label for="zp-MetazotMetaBox-InText-Options-Title-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Title-No" name="title" value="no" checked="checked">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Downloadable','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-InText-Options-Download-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Download-Yes" name="download" value="yes">

                        <label for="zp-MetazotMetaBox-InText-Options-Download-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Download-No" name="download" value="no" checked="checked">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Abstract','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-InText-Options-Abstract-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Abstract-Yes" name="abstract" value="yes">

                        <label for="zp-MetazotMetaBox-InText-Options-Abstract-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Abstract-No" name="abstract" value="no" checked="checked">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Notes','metazot'); ?>?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-InText-Options-Notes-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Notes-Yes" name="notes" value="yes">

                        <label for="zp-MetazotMetaBox-InText-Options-Notes-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Notes-No" name="notes" value="no" checked="checked">
                    </div>
                </div>

                <hr>

                <div class="zp-MetazotMetaBox-Field">
                    <?php _e('Cite with RIS','metazot'); ?>S?
                    <div class="zp-MetazotMetaBox-Field-Radio">
                        <label for="zp-MetazotMetaBox-InText-Options-Cite-Yes"><?php _e('Yes','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Cite-Yes" name="cite" value="yes">

                        <label for="zp-MetazotMetaBox-InText-Options-Cite-No"><?php _e('No','metazot'); ?></label>
                        <input type="radio" id="zp-MetazotMetaBox-InText-Options-Cite-No" name="cite" value="no" checked="checked">
                    </div>
                </div>

            </div>
        </div>
        <!-- END OF OPTIONS -->

        <!-- START OF SHORTCODE -->
        <div id="zp-MetazotMetaBox-InText-Generate">

            <a id="zp-MetazotMetaBox-InText-Generate-Button" class="button-primary" href="javascript:void(0);"><?php _e('Generate Shortcode','metazot'); ?></a>
            <a id="zp-MetazotMetaBox-InText-Clear-Button" class="button" href="javascript:void(0);"><?php _e('Clear','metazot'); ?></a>

            <hr class="clear">

            <div id="zp-MetazotMetaBox-InText-Generate-Inner">
                <label for="zp-MetazotMetaBox-InText-InText"><?php _e('Shortcode','metazot'); ?>:</span></label>
                <textarea id="zp-MetazotMetaBox-InText-InText">[metazotInText]</textarea>

                <div id="zp-MetazotMetaBox-InText-Text-Bib-Container" class="inTextOnly">
                    <label for="zp-MetazotMetaBox-InText-Text-Bib"><?php _e('Bibliography','metazot'); ?>: <span><?php _e('Paste somewhere in the post','metazot'); ?></span></label>
                    <input id="zp-MetazotMetaBox-InText-Text-Bib" type="text" value="[metazotInTextBib]">
                </div>
            </div>
        </div>
        <!-- END OF SHORTCODE -->

    </div><!-- #zp-MetazotMetaBox-InText -->

    <!-- END OF METAZOT IN-TEXT ---------------------------------------------------------------------------->



</div><!-- #zp-MetazotMetaBox -->

<!-- END OF METAZOT METABOX ------------------------------------------------------------------------------->
