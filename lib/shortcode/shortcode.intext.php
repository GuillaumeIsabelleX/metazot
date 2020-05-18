<?php

    function Metazot_metazotInText ($atts)
    {
        /*
        *   GLOBAL VARIABLES
        *
        *   $GLOBALS['mz_shortcode_instances'] {instantiated in metazot.php}
        *
        */

        extract(shortcode_atts(array(

            'item' => false,
            'items' => false,

            'pages' => false,
            'format' => "(%a%, %d%, %p%)",
			'brackets' => false,
            'etal' => false, // default (false), yes, no
            'separator' => false, // default (comma), semicolon
            'and' => false, // default (no), and, comma-and

            'userid' => false,
            'api_user_id' => false,
            'nickname' => false,
            'nick' => false

        ), $atts));



        // PREPARE ATTRIBUTES

        if ($items) $items = str_replace(" ", "", str_replace('"','',html_entity_decode($items)));
        else if ($item) $items = str_replace(" ", "", str_replace('"','',html_entity_decode($item)));

        $pages = str_replace('"','',html_entity_decode($pages));
        $format = str_replace('"','',html_entity_decode($format));
        $brackets = str_replace('"','',html_entity_decode($brackets));

        $etal = str_replace('"','',html_entity_decode($etal));
        if ($etal == "default") { $etal = false; }

        $separator = str_replace('"','',html_entity_decode($separator));
        if ($separator == "default") { $separator = false; }

        $and = str_replace('"','',html_entity_decode($and));
        if ($and == "default") { $and = false; }

        if ($userid) { $api_user_id = str_replace('"','',html_entity_decode($userid)); }
        if ($nickname) { $nickname = str_replace('"','',html_entity_decode($nickname)); }
        if ($nick) { $nickname = str_replace('"','',html_entity_decode($nick)); }



        // GET ACCOUNTS

        global $wpdb;

        // Turn on/off minified versions if testing/live
        $minify = ''; if ( METAZOT_LIVEMODE ) $minify = '.min';

		wp_enqueue_script( 'metazot.shortcode.intext'.$minify.'.js' );

        $mz_account = false;

        if ($nickname !== false)
        {
            $mz_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot WHERE nickname='".$nickname."'", OBJECT);
            $api_user_id = $mz_account->api_user_id;
        }
        else if ($api_user_id !== false)
        {
            $mz_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot WHERE api_user_id='".$api_user_id."'", OBJECT);
            $api_user_id = $mz_account->api_user_id;
        }
        else if ($api_user_id === false && $nickname === false)
        {
            if (get_option("Metazot_DefaultAccount") !== false)
            {
                $api_user_id = get_option("Metazot_DefaultAccount");
                $mz_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot WHERE api_user_id ='".$api_user_id."'", OBJECT);
            }
            else // When all else fails ...
            {
                $mz_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."metazot LIMIT 1", OBJECT);
                $api_user_id = $mz_account->api_user_id;
            }
        }


        // Generate instance id for shortcode
		$mz_instance_id = "zp-ID-".$api_user_id."-" . str_replace( " ", "_", str_replace( "&", "_", str_replace( "+", "_", str_replace( "/", "_", str_replace( "{", "-", str_replace( "}", "-", str_replace( ",", "_", $items ) ) ) ) ) ) ) ."-".get_the_ID();

		if ( ! isset( $GLOBALS['mz_shortcode_instances'][get_the_ID()] ) )
			$GLOBALS['mz_shortcode_instances'][get_the_ID()] = array();

        $GLOBALS['mz_shortcode_instances'][get_the_ID()][] = array( "instance_id" => $mz_instance_id, "api_user_id" =>$api_user_id, "items" => $items );


		// Show theme scripts
		$GLOBALS['mz_is_shortcode_displayed'] = true;

		// Output attributes and loading
		return '<span id="zp-InText-'.$mz_instance_id."-".count($GLOBALS['mz_shortcode_instances'][get_the_ID()]).'"
						class="zp-InText-Citation loading"
						rel="{ \'api_user_id\': \''.$api_user_id.'\', \'pages\': \''.$pages.'\', \'items\': \''.$items.'\', \'format\': \''.$format.'\', \'brackets\': \''.$brackets.'\', \'etal\': \''.$etal.'\', \'separator\': \''.$separator.'\', \'and\': \''.$and.'\' }"></span>';
    }


?>
