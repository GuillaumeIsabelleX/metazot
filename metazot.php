<?php

/*

    Plugin Name: Metazot
    Plugin URI: http://guillaumeisabelle.com/plugins
    Description: Bringing Zotero and scholarly blogging to your WordPress website.
    Author: Katie Seaborn, Jean Guillaume Isabelle
    Version: 7.0.3
    Author URI: http://katieseaborn.com
    Text Domain: metazot
    Domain Path: /languages/

*/

/*

    Copyright 2018 Katie Seaborn, Jean Guillaume Isabelle

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

        http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.

*/



// GLOBAL VARS ----------------------------------------------------------------------------------

    define('METAZOT_PLUGIN_FILE',  __FILE__ );
    define('METAZOT_PLUGIN_URL', plugin_dir_url( METAZOT_PLUGIN_FILE ));
    define('METAZOT_PLUGIN_DIR', dirname( __FILE__ ));
    define('METAZOT_VERSION', '7.0.3' );
    define('METAZOT_LIVEMODE', true ); // NOTE: REMEMBER to set to TRUE

    $GLOBALS['mz_is_shortcode_displayed'] = false;
    $GLOBALS['mz_shortcode_instances'] = array();

    $GLOBALS['METAZOT_update_db_by_version'] = '6.2'; // NOTE: Only change this if the db needs updating - 5.2.6

// GLOBAL VARS ----------------------------------------------------------------------------------



// LOCALIZATION ----------------------------------------------------------------------------------

// TODO: Apply localization to the entire plugin.
// TODO: Don't forget JS files, which have a special procedure.

function METAZOT_load_plugin_textdomain() {
  load_plugin_textdomain( 'metazot', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'METAZOT_load_plugin_textdomain' );

// LOCALIZATION ----------------------------------------------------------------------------------



// INSTALL -----------------------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/admin/admin.install.php' );

// INSTALL -----------------------------------------------------------------------------------------



// ADMIN -------------------------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/admin/admin.php' );

    add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'METAZOT_add_plugin_page_settings_link');
    function METAZOT_add_plugin_page_settings_link( $links ) {
        $links[] = '<a href="' .
        admin_url( 'admin.php?page=Metazot' ) .
        '">' . __('Explore') . '</a>';
        return $links;
    }

// END ADMIN --------------------------------------------------------------------------------------



// SHORTCODE -------------------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/shortcode/shortcode.php' );
    include( dirname(__FILE__) . '/lib/shortcode/shortcode.intext.php' );
    include( dirname(__FILE__) . '/lib/shortcode/shortcode.intextbib.php' );
    include( dirname(__FILE__) . '/lib/shortcode/shortcode.lib.php' );

// SHORTCODE -------------------------------------------------------------------------------------



// WIDGETS -----------------------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/widget/widget.sidebar.php' );
	include( dirname(__FILE__) . '/lib/widget/widget.php' );

// WIDGETS -----------------------------------------------------------------------------------------



// REGISTER ACTIONS -----------------------------------------------------------------------------

    /**
    * Admin scripts and styles
    */
    function METAZOT_admin_scripts_css($hook)
    {
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( METAZOT_LIVEMODE ) $minify = '.min';

		if ( isset($_GET['page']) && ($_GET['page'] == 'Metazot') )
		{
			wp_enqueue_script( 'jquery' );
			wp_enqueue_media();
			wp_enqueue_script( 'jquery.dotimeout.min.js', METAZOT_PLUGIN_URL . 'js/jquery.dotimeout.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'metazot.default'.$minify.'.js', METAZOT_PLUGIN_URL . 'js/metazot.default'.$minify.'.js', array( 'jquery' ) );

			if ( in_array( $hook, array('post.php', 'post-new.php') ) !== true )
			{
				wp_enqueue_script( 'jquery.livequery.min.js', METAZOT_PLUGIN_URL . 'js/jquery.livequery.min.js', array( 'jquery' ) );
			}

			if ( isset($_GET['help']) && ($_GET['help'] == 'true') )
			{
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-tabs' );
				wp_enqueue_style( 'metazot.help'.$minify.'.css', METAZOT_PLUGIN_URL . 'css/metazot.help'.$minify.'.css' );
				wp_enqueue_script( 'metazot.help.min.js', METAZOT_PLUGIN_URL . 'js/metazot.help.min.js', array( 'jquery' ) );
			}

			wp_enqueue_style( 'metazot'.$minify.'.css', METAZOT_PLUGIN_URL . 'css/metazot'.$minify.'.css' );
		}
    }
    add_action( 'admin_enqueue_scripts', 'METAZOT_admin_scripts_css' );


	function METAZOT_enqueue_admin_ajax( $hook )
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( METAZOT_LIVEMODE ) $minify = '.min';

		if ( strpos( strtolower($hook), "metazot" ) !== false )
		{
			wp_enqueue_script( 'metazot.admin'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/metazot.admin'.$minify.'.js', array( 'jquery','media-upload','thickbox' ) );
			wp_localize_script(
				'metazot.admin'.$minify.'.js',
				'zpAccountsAJAX',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'zpAccountsAJAX_nonce' => wp_create_nonce( 'zpAccountsAJAX_nonce_val' ),
					'action' => 'zpAccountsViaAJAX',
                    'txt_success' => __('Success','metazot'),
                    'txt_chooseimg' => __('Choose Image','metazot'),
                    'txt_accvalid' => __('Your Zotero account has been validated.','metazot'),
                    'txt_sureremove' => __('Are you sure you want to remove this account?','metazot'),
                    'txt_surecache' => __('Are you sure you want to clear the cache for this account?','metazot'),
                    'txt_cachecleared' => __('Cache cleared!','metazot'),
                    'txt_oops' => __('Oops!','metazot'),
                    'txt_surereset' => __('Are you sure you want to reset Metazot? This cannot be undone.','metazot'),
                    'txt_default' => __('Default','metazot')
				)
			);
			wp_enqueue_script( 'metazot.admin.notices'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/metazot.admin.notices'.$minify.'.js', array( 'jquery' ) );
			wp_localize_script(
				'metazot.admin.notices'.$minify.'.js',
				'zpNoticesAJAX',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'zpNoticesAJAX_nonce' => wp_create_nonce( 'zpNoticesAJAX_nonce_val' ),
					'action' => 'zpNoticesViaAJAX'
				)
			);
		}
	}
    add_action( 'admin_enqueue_scripts', 'METAZOT_enqueue_admin_ajax' );


    /**
    * Add Metazot to admin menu
    */
    function METAZOT_admin_menu()
    {
        add_menu_page( "Metazot", "Metazot", "edit_posts", "Metazot", "METAZOT_options", METAZOT_PLUGIN_URL."images/icon-menu.svg" );
		add_submenu_page( "Metazot", "Metazot", __('Browse','metazot'), "edit_posts", "Metazot" );
		add_submenu_page( "Metazot", "Accounts", __('Accounts','metazot'), "edit_posts", "admin.php?page=Metazot&accounts=true" );
		add_submenu_page( "Metazot", "Options", __('Options','metazot'), "edit_posts", "admin.php?page=Metazot&options=true" );
		add_submenu_page( "Metazot", "Help", __('Help','metazot'), "edit_posts", "admin.php?page=Metazot&help=true" );
    }
    add_action( 'admin_menu', 'METAZOT_admin_menu' );

	function METAZOT_admin_menu_submenu($parent_file)
	{
		global $submenu_file;

		if ( isset($_GET['accounts']) || isset($_GET['selective'])  || isset($_GET['import']) ) $submenu_file = 'admin.php?page=Metazot&accounts=true';
		if ( isset($_GET['options']) ) $submenu_file = 'admin.php?page=Metazot&options=true';
		if ( isset($_GET['help']) ) $submenu_file = 'admin.php?page=Metazot&help=true';

		return $parent_file;
	}
	add_filter('parent_file', 'METAZOT_admin_menu_submenu');


    /**
    * Add shortcode styles to user's theme
    * Note that this always displays: There's no way to conditionally include it,
    * because the existence of shortcodes is checked after CSS is included.
    */
    function METAZOT_theme_includes()
    {
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( METAZOT_LIVEMODE ) $minify = '.min';

        wp_register_style('metazot.shortcode'.$minify.'.css', METAZOT_PLUGIN_URL . 'css/metazot.shortcode'.$minify.'.css');
        wp_enqueue_style('metazot.shortcode'.$minify.'.css');
    }
    add_action('wp_print_styles', 'METAZOT_theme_includes');


    /**
    * Change HTTP request timeout
    */
    function METAZOT_change_timeout($time) { return 60; /* second */ }
    add_filter('http_request_timeout', 'METAZOT_change_timeout');



    // Enqueue jQuery in theme if it isn't already enqueued
    // Thanks to WordPress user "eceleste"
    function METAZOT_enqueue_scripts()
    {
        if ( ! isset( $GLOBALS['wp_scripts']->registered[ "jquery" ] ) )
            wp_enqueue_script("jquery");
    }
    add_action( 'wp_enqueue_scripts' , 'METAZOT_enqueue_scripts' );

    // Add shortcodes and sidebar widget
    add_shortcode( 'metazot', 'METAZOT_func' );
    add_shortcode( 'metazotInText', 'METAZOT_metazotInText' );
    add_shortcode( 'metazotInTextBib', 'METAZOT_metazotInTextBib' );
    add_shortcode( 'metazotLib', 'METAZOT_metazotLib' );
    add_action( 'widgets_init', 'MetazotSidebarWidgetInit' );

    // Conditionally serve shortcode scripts
    function METAZOT_theme_conditional_scripts_footer()
    {
        if ( $GLOBALS['mz_is_shortcode_displayed'] === true )
        {
            if ( !is_admin() ) wp_enqueue_script('jquery');
            wp_register_script('jquery.livequery.min.js', METAZOT_PLUGIN_URL . 'js/jquery.livequery.min.js', array('jquery'));
            wp_enqueue_script('jquery.livequery.min.js');

			wp_enqueue_script("jquery-effects-core");
			wp_enqueue_script("jquery-effects-highlight");
        }
    }
    add_action('wp_footer', 'METAZOT_theme_conditional_scripts_footer');


	function METAZOT_enqueue_shortcode_bib()
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( METAZOT_LIVEMODE ) $minify = '.min';

		wp_register_script( 'metazot.shortcode.bib'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/metazot.shortcode.bib'.$minify.'.js', array( 'jquery' ), false, true );
		wp_localize_script(
			'metazot.shortcode.bib'.$minify.'.js',
			'zpShortcodeAJAX',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'zpShortcode_nonce' => wp_create_nonce( 'zpShortcode_nonce_val' ),
				'action' => 'zpRetrieveViaShortcode'
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'METAZOT_enqueue_shortcode_bib' );


	function METAZOT_enqueue_shortcode_intext()
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( METAZOT_LIVEMODE ) $minify = '.min';

		wp_register_script( 'metazot.shortcode.intext'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/metazot.shortcode.intext'.$minify.'.js', array( 'jquery' ), false, true );
		wp_localize_script(
			'metazot.shortcode.intext'.$minify.'.js',
			'zpShortcodeAJAX',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'zpShortcode_nonce' => wp_create_nonce( 'zpShortcode_nonce_val' ),
				'action' => 'zpRetrieveViaShortcode'
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'METAZOT_enqueue_shortcode_intext' );


	function METAZOT_enqueue_lib_dropdown()
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( METAZOT_LIVEMODE ) $minify = '.min';

		wp_register_script( 'metazot.lib'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/metazot.lib'.$minify.'.js', array( 'jquery' ), false, true );
		wp_register_script( 'metazot.lib.dropdown'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/metazot.lib.dropdown'.$minify.'.js', array( 'jquery' ), false, true );
		wp_localize_script(
			'metazot.lib.dropdown'.$minify.'.js',
			'zpShortcodeAJAX',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'zpShortcode_nonce' => wp_create_nonce( 'zpShortcode_nonce_val' ),
				'action' => 'zpRetrieveViaShortcode',
                'txt_loading' => __( 'Loading', 'metazot' ),
                'txt_items' => __( 'items', 'metazot' ),
                'txt_subcoll' => __( 'subcollections', 'metazot' ),
                'txt_changeimg' => __( 'Change Image', 'metazot' ),
                'txt_setimg' => __( 'Set Image', 'metazot' ),
                'txt_itemkey' => __( 'Item Key', 'metazot' ),
                'txt_nocitations' => __( 'There are no citations to display.', 'metazot' ),
                'txt_toplevel' => __( 'Top Level', 'metazot' ),
                'txt_nocollsel' => __( 'No Collection Selected', 'metazot' ),
                'txt_backtotop' => __( 'Back to Top', 'metazot' ),
                'txt_notagsel' => __( 'No Tag Selected', 'metazot' ),
                'txt_notags' => __( 'No tags to display', 'metazot' )
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'METAZOT_enqueue_lib_dropdown' );
	add_action( 'admin_enqueue_scripts', 'METAZOT_enqueue_lib_dropdown' );


	function METAZOT_enqueue_lib_searchbar()
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( METAZOT_LIVEMODE ) $minify = '.min';

		wp_register_script( 'metazot.lib'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/metazot.lib'.$minify.'.js', array( 'jquery' ), false, true );
		wp_register_script( 'metazot.lib.searchbar'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/metazot.lib.searchbar'.$minify.'.js', array( 'jquery' ), false, true );
		wp_localize_script(
			'metazot.lib.searchbar'.$minify.'.js',
			'zpShortcodeAJAX',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'zpShortcode_nonce' => wp_create_nonce( 'zpShortcode_nonce_val' ),
				'action' => 'zpRetrieveViaShortcode',
                'txt_typetosearch' => __('Type to search','metazot')
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'METAZOT_enqueue_lib_searchbar' );
	add_action( 'admin_enqueue_scripts', 'METAZOT_enqueue_lib_searchbar' );

// REGISTER ACTIONS 	---------------------------------------------------------------------------------


// SECURITY 	----------------------------------------------------------------------------------------------

	function mz_nonce_life() {
		return 24 * HOUR_IN_SECONDS;
	}
	add_filter( 'nonce_life', 'mz_nonce_life' );

// SECURITY 	----------------------------------------------------------------------------------------------


// METAZOT 6.2.1 NOTIFICATION 	------------------------------------------------------------------------

    if ( in_array( METAZOT_VERSION, array( "6.2.1", "6.2.2") ) )
    {
        if ( ! get_option( 'METAZOT_update_notice_dismissed' ) )
            add_action( 'admin_notices', 'METAZOT_update_notice' );

        function METAZOT_update_notice()
        {
        ?>
            <div class="notice update-nag METAZOT_update_notice is-dismissible" >
                <p><?php __( 'Warning: Due to major updates in Metazot 6.2, you may need to clear the cache of each synced Zotero account.', 'metazot' ); ?></p>
            </div>
        <?php
        }

        function METAZOT_dismiss_update_notice()
        {
            if ( ! get_option( 'METAZOT_update_notice_dismissed' )
                    || get_option( 'METAZOT_update_notice_dismissed' ) == 0 )
                update_option( 'METAZOT_update_notice_dismissed', 1 );
        }
        add_action( 'wp_ajax_zpNoticesViaAJAX', 'METAZOT_dismiss_update_notice' );
    }

// METAZOT 6.2.1 NOTIFICATION 	------------------------------------------------------------------------


?>
