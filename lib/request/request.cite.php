<?php

	// Include WordPress
	require('../../../../../wp-load.php');
	define('WP_USE_THEMES', false);

	// Include Request Functionality
	require('request.class.php');
	require('request.functions.php');

	// Content prep
	$mz_xml = false;


	// item key
	if (isset($_GET['item_key']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['item_key']))
		$mz_item_key = trim(urldecode($_GET['item_key']));
	else
		$mz_xml = "No item key provided.";

	// Api User ID
	if (isset($_GET['api_user_id']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['api_user_id']))
		$mz_api_user_id = trim(urldecode($_GET['api_user_id']));
	else
		$mz_xml = "No API User ID provided.";


	// Get cite data from Zotero
	if ($mz_xml === false)
	{
		// Access WordPress db
		global $wpdb;

		// Get account
		$mz_account = mz_get_account ($wpdb, $mz_api_user_id);

		// Build import structure
		$mz_import_contents = new MetazotRequest();
		$mz_import_url = "https://api.zotero.org/".$mz_account[0]->account_type."/".$mz_api_user_id."/items/".$mz_item_key."?format=ris&key=".$mz_account[0]->public_key;

		// Read the external data
        $mz_xml = $mz_import_contents->get_request_contents( $mz_import_url, true, false, 'ris' );

		if ( $mz_xml !== false && strlen(trim($mz_xml["json"])) > 0 )
		{
			header('Content-Type: application/x-research-info-systems');
			header('Content-Disposition: attachment; filename="itemkey-'.$mz_item_key.'.ris"');
			header('Content-Description: Cite with RIS');
			echo $mz_xml["json"];
		}
		else
		{
			echo "No cite file found.";
		}
	}
	else
	{
		echo $mz_xml;
	}
?>
