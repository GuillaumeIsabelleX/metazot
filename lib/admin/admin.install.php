<?php

// INSTALL -----------------------------------------------------------------------------------------

    function Metazot_install()
    {
        global $wpdb;

        $Metazot_main_db_version = "5.2";
        $Metazot_oauth_db_version = "5.0.5";
        $Metazot_zoteroItemImages_db_version = "5.2.6";
		$Metazot_cache_version = "6.2";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


		// REMOVE OLD DATABASES AND CHECKS - since 6.0
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroItems;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroCollections;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroTags;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroRelItemColl;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroRelItemTags;");
        delete_option( 'Metazot_zoteroItems_db_version' );
        delete_option( 'Metazot_zoteroCollections_db_version' );
        delete_option( 'Metazot_zoteroTags_db_version' );
        delete_option( 'Metazot_zoteroRelItemColl_db_version' );
        delete_option( 'Metazot_zoteroRelItemTags_db_version' );


        // ZOTERO ACCOUNTS TABLE

		/**
		 * For each table, the basic check is:
		 *
		 * If the table version option doesn't exist, OR
		 * If the table version is not the same as the update version (variables defined above)
		 *
		 * Then add/update the table and add/update the option
		 */

        if
			(
				!get_option("Metazot_main_db_version")
                || get_option("Metazot_main_db_version") != $Metazot_main_db_version
            )
        {
			$table_name = $wpdb->prefix . "metazot";

            $structure = "CREATE TABLE $table_name (
                id INT(9) NOT NULL AUTO_INCREMENT,
                account_type VARCHAR(10) NOT NULL,
                api_user_id VARCHAR(10) NOT NULL,
                public_key VARCHAR(28) default NULL,
                nickname VARCHAR(200) default NULL,
                version VARCHAR(10) default '5.1',
                UNIQUE KEY id (id)
            );";

            dbDelta($structure);

            update_option("Metazot_main_db_version", $Metazot_main_db_version);
        }


        // OAUTH CACHE TABLE

        if (!get_option("Metazot_oauth_db_version")
                || get_option("Metazot_oauth_db_version") != $Metazot_oauth_db_version
                )
        {
			$table_name = $wpdb->prefix . "metazot_oauth";

            $structure = "CREATE TABLE $table_name (
                id INT(9) NOT NULL AUTO_INCREMENT,
                cache LONGTEXT NOT NULL,
                UNIQUE KEY id (id)
            );";

            dbDelta($structure);

            update_option("Metazot_oauth_db_version", $Metazot_oauth_db_version);

            // Initial populate
            if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."metazot_oauth;") == 0)
                $wpdb->query("INSERT INTO ".$wpdb->prefix."metazot_oauth (cache) VALUES ('empty')");
        }



        // ZOTERO ITEM IMAGES TABLE

        if ( ! get_option("Metazot_zoteroItemImages_db_version")
                || get_option("Metazot_zoteroItemImages_db_version") != $Metazot_zoteroItemImages_db_version
           )
        {
			$table_name = $wpdb->prefix . "metazot_zoteroItemImages";

            $structure = "CREATE TABLE $table_name (
                id INT(9) AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                item_key VARCHAR(50),
                image TEXT,
                UNIQUE KEY id (id),
                PRIMARY KEY (api_user_id, item_key)
            );";

            dbDelta( $structure );

            update_option( "Metazot_zoteroItemImages_db_version", $Metazot_zoteroItemImages_db_version );
        }


        // ZOTERO CACHE TABLE

        if ( ! get_option("Metazot_cache_version")
				|| get_option("Metazot_cache_version") != $Metazot_cache_version )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."metazot_cache (
                id INT(9) NOT NULL AUTO_INCREMENT,
				request_id VARCHAR(200) NOT NULL,
                api_user_id VARCHAR(50),
                json BLOB,
                headers MEDIUMTEXT,
                libver INT(9),
                retrieved VARCHAR(100),
                UNIQUE KEY id (id),
				PRIMARY KEY (request_id)
            );";

            dbDelta($structure);

            update_option("Metazot_cache_version", $Metazot_cache_version);
        }

	}
    register_activation_hook( METAZOT_PLUGIN_FILE, 'Metazot_install' );

// INSTALL -----------------------------------------------------------------------------------------


// UNINSTALL --------------------------------------------------------------------------------------

    function Metazot_deactivate()
    {
        global $wpdb;
        global $current_user;

        // Drop all tables -- originally not including accounts/main, but not sure why
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_oauth;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroItems;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroItemImages;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroCollections;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroTags;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroRelItemColl;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_zoteroRelItemTags;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."metazot_cache;");

        // Delete options
        delete_option( 'Metazot_DefaultCPT' );
        delete_option( 'Metazot_DefaultAccount' );
        delete_option( 'Metazot_DefaultStyle' );
        delete_option( 'Metazot_StyleList' );
        delete_option( 'Metazot_update_version' );
        delete_option( 'Metazot_main_db_version' );
        delete_option( 'Metazot_oauth_db_version' );
        delete_option( 'Metazot_zoteroItems_db_version' );
        delete_option( 'Metazot_zoteroCollections_db_version' );
        delete_option( 'Metazot_zoteroTags_db_version' );
        delete_option( 'Metazot_zoteroRelItemColl_db_version' );
        delete_option( 'Metazot_zoteroRelItemTags_db_version' );
		delete_option( 'Metazot_zoteroItemImages_db_version' );
		delete_option( 'Metazot_cache_version' );
		delete_option( 'Metazot_update_notice_dismissed' );

        // Delete user meta
        delete_user_meta( $current_user->ID, 'metazot_5_2_ignore_notice' );
        delete_user_meta( $current_user->ID, 'metazot_survey_notice_ignore' );
    }

    register_uninstall_hook( METAZOT_PLUGIN_FILE, 'Metazot_deactivate' );

// UNINSTALL ---------------------------------------------------------------------------------------


// UPDATE ------------------------------------------------------------------------------------------


	/**
	 *
	 * If update check option doesn't exist, OR
	 * If it exists but it's not the same version as the database update version
	 *
	 * Then, run the install, which installs or updates the databases
	 *
	**/
    if ( ! get_option( "Metazot_update_version" )
			|| get_option("Metazot_update_version") != $GLOBALS['Metazot_update_db_by_version'] )
    {
        Metazot_install();

        // Add or update version number
        if ( !get_option( "Metazot_update_version" ) )
            add_option( "Metazot_update_version", $GLOBALS['Metazot_update_db_by_version'], "", "no" );
        else
            update_option( "Metazot_update_version", $GLOBALS['Metazot_update_db_by_version'] );
    }

// UPDATE ------------------------------------------------------------------------------------------


?>
