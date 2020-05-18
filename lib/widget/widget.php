<?php


	// Add Widget Metabox
    function Metazot_add_meta_box()
    {
        $mz_default_cpt = "post,page";
        if (get_option("Metazot_DefaultCPT"))
            $mz_default_cpt = get_option("Metazot_DefaultCPT");
        $mz_default_cpt = explode(",",$mz_default_cpt);

        foreach ($mz_default_cpt as $post_type )
        {
            add_meta_box(
                'MetazotMetaBox',
                __( 'Metazot Reference', 'metazot' ),
                'Metazot_show_meta_box',
                $post_type,
                'side'
            );
        }
    }
    add_action('admin_init', 'Metazot_add_meta_box', 1); // backwards compatible

    function Metazot_show_meta_box() { require( dirname(__FILE__) . '/widget.metabox.php'); }



	// Set up Widget Metabox AJAX search
	function Metazot_widget_metabox_AJAX_search()
	{
		global $wpdb;

		// Determine account based on passed account
        if ( $_GET['api_user_id'] && is_numeric($_GET['api_user_id']) )
        {
            $mz_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot WHERE api_user_id='".$_GET['api_user_id']."'", OBJECT);
            $mz_api_user_id = $mz_account->api_user_id;
            $mz_nickname = $mz_account->nickname;
        }
        // If, for some reason, the account isn't passed through
        else
        {
            if (get_option("Metazot_DefaultAccount"))
    		{
    			$mz_api_user_id = get_option("Metazot_DefaultAccount");
                $mz_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot WHERE api_user_id='".$mz_api_user_id."'", OBJECT);
                $mz_nickname = $mz_account->nickname;
    		}
    		else
    		{
    			$mz_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot LIMIT 1", OBJECT);
    			$mz_api_user_id = $mz_account->api_user_id;
                $mz_nickname = $mz_account->nickname;
    		}
        }

		$zpSearch = array();

		// Include relevant classes and functions
		include( dirname(__FILE__) . '/../request/request.class.php' );
		include( dirname(__FILE__) . '/../request/request.functions.php' );

		// Set up Metazot request
		$mz_import_contents = new MetazotRequest();

		// Get account
		$mz_account = mz_get_account ($wpdb, $mz_api_user_id);

		// Format Zotero request URL
		// e.g., https://api.zotero.org/users/#####/items?key=###&format=json&q=###&limit=25
		$mz_import_url = "https://api.zotero.org/".$mz_account[0]->account_type."/".$mz_account[0]->api_user_id."/items?";
		if (is_null($mz_account[0]->public_key) === false && trim($mz_account[0]->public_key) != "")
			$mz_import_url .= "key=".$mz_account[0]->public_key."&";
		$mz_import_url .= "format=json&q=".urlencode($_GET['term'])."&limit=10&itemType=-attachment+||+note";

		// Read the external data
		$mz_xml = $mz_import_contents->get_request_contents( $mz_import_url, true ); // Unsure about "true"
		$zpResultJSON = json_decode( $mz_xml["json"] );

		if ( count($zpResultJSON) > 0 )
		{
			foreach ( $zpResultJSON as $zpResult )
			{
				// Deal with author(s)
				$author = "N/A";
				if ( isset( $zpResult->data->creators ) )
				{
					$author = "";
					foreach ( $zpResult->data->creators as $i => $creator)
					{
						if ( isset( $creator->name ) )
							$author .= $creator->name;
						else
							$author .= $creator->lastName;

						if ( $i != (count($zpResult->data->creators)-1) ) $author .= ', ';
					}
				}

				// Deal with label
				// e.g., (year). title
				$label = " (";
				if ( isset( $zpResult->data->date ) && trim($zpResult->data->date) != "" ) $label .= $zpResult->data->date; else $label .= "n.d.";
				$label .= "). ";
				$title = "Untitled."; if ( isset( $zpResult->data->title ) && trim($zpResult->data->title) != "" ) $title = $zpResult->data->title . ".";

				// If no author, use title
				if ( trim($author) == "" )
				{
					$author = $title;
					$title = "";
				}
				$label = $label . $title;

				array_push( $zpSearch, array( "api_user_id" => $mz_api_user_id, "nickname" => $mz_nickname, "author" => $author, "label" => $label, "value" => $zpResult->key) );
			}
		}

		unset($mz_import_contents);
		unset($mz_import_url);
		unset($mz_xml);


		$response = json_encode($zpSearch);
		echo $response;

		unset($mz_api_user_id);
		unset($mz_account);
		$wpdb->flush();

		exit();
    }
    add_action( 'wp_ajax_zpWidgetMetabox-submit', 'Metazot_widget_metabox_AJAX_search' );



	// Set relevant admin-level Widget Metabox scripts
	function Metazot_zpWidgetMetabox_scripts_css($hook)
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( METAZOT_LIVEMODE ) $minify = '.min';

        if ( in_array( $hook, array('post.php', 'post-new.php') ) === true )
        {
            wp_enqueue_script( 'jquery.livequery.min.js', METAZOT_PLUGIN_URL . 'js/jquery.livequery.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-tabs', 'jquery-ui-autocomplete' ) );
            wp_enqueue_script( 'metazot.widget.metabox'.$minify.'.js', METAZOT_PLUGIN_URL . 'js/metazot.widget.metabox'.$minify.'.js', array( 'jquery', 'jquery-form', 'json2' ) );

			wp_localize_script(
				'metazot.widget.metabox'.$minify.'.js',
				'zpWidgetMetabox',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'zpWidgetMetabox_nonce' => wp_create_nonce( 'zpWidgetMetabox_nonce_val' ),
					'action' => 'zpWidgetMetabox-submit',
                    'txt_typetosearch' => __( 'Type to search', 'metazot' ),
                    'txt_pages' => __( 'Page(s)', 'metazot' ),
                    'txt_itemkey' => __( 'Item Key', 'metazot' ),
                    'txt_account' => __( 'Account', 'metazot' )
				)
			);
        }
	}
	add_action( 'admin_enqueue_scripts', 'Metazot_zpWidgetMetabox_scripts_css' );



    /**
    * Metabox styles
    */
    function Metazot_admin_post_styles()
    {
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( METAZOT_LIVEMODE ) $minify = '.min';

        wp_register_style('metazot.metabox'.$minify.'.css', METAZOT_PLUGIN_URL . 'css/metazot.metabox'.$minify.'.css');
        wp_enqueue_style('metazot.metabox'.$minify.'.css');

        wp_enqueue_style('jquery-ui-tabs', METAZOT_PLUGIN_URL . 'css/smoothness/jquery-ui-1.8.11.custom'.$minify.'.css');
    }
    add_action('admin_print_styles-post.php', 'Metazot_admin_post_styles');
    add_action('admin_print_styles-post-new.php', 'Metazot_admin_post_styles');


?>
