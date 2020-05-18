<?php

	// Include WordPress
	require('../../../../../wp-load.php');
	define('WP_USE_THEMES', false);

	// Include Request Functionality
	require('request.class.php');
	require('request.functions.php');

	// Content prep
	$mz_xml = false;

	// Key
	if (isset($_GET['dlkey']) && preg_match("/^[a-zA-Z0-9]{3,40}$/", $_GET['dlkey']))
		$mz_item_key = trim(urldecode($_GET['dlkey']));
	else
		$mz_xml = "No key provided, or format incorrect.";

	// Api User ID
	if (isset($_GET['api_user_id']) && preg_match("/^[a-zA-Z0-9]{3,15}$/", $_GET['api_user_id']))
		$mz_api_user_id = trim(urldecode($_GET['api_user_id']));
	else
		$mz_xml = "No API User ID provided, or format incorrect.";

	// Content type
	if (isset($_GET['content_type']) && preg_match("/^[a-zA-Z0-9\/]{3,40}$/", $_GET['content_type']))
		$mz_content_type = trim(urldecode($_GET['content_type']));
	else
		$mz_xml = "No content type provided, or format incorrect.";


	if ($mz_xml === false)
	{
		// Access WordPress db
		global $wpdb;

		// Get account
		$mz_account = mz_get_account ($wpdb, $mz_api_user_id);

		// Build import structure
		$mz_import_contents = new MetazotRequest();

		$mz_import_url = "https://api.zotero.org/".$mz_account[0]->account_type."/".$mz_api_user_id."/items/";
		$mz_import_url .= $mz_item_key."/file/view?key=".$mz_account[0]->public_key;

		// Read the external data
        $mz_xml = $mz_import_contents->get_request_contents( $mz_import_url, true ); // Unsure about "true"

		// Determine filename based on content type
		$mz_filename ="download-".$mz_item_key.".";
		if ( strpos( $mz_content_type, "pdf" ) ) $mz_filename .= "pdf";
		else if ( strpos( $mz_content_type, "wordprocessingml.document" ) ) $mz_filename .= "docx";
		else if ( strpos( $mz_content_type, "msword" ) ) $mz_filename .= "doc";
		else if ( strpos( $mz_content_type, "latex" ) ) $mz_filename .= "latex";
		else if ( strpos( $mz_content_type, "presentationml.presentation" ) ) $mz_filename .= "pptx";
		else if ( strpos( $mz_content_type, "ms-powerpointtd" ) ) $mz_filename .= "ppt";
		else if ( strpos( $mz_content_type, "rtf" ) ) $mz_filename .= "rtf";
		else if ( strpos( $mz_content_type, "opendocument.text" ) ) $mz_filename .= "odt";
		else if ( strpos( $mz_content_type, "opendocument.presentation" ) ) $mz_filename .= "odp";

		if ( $mz_xml !== false && strlen(trim($mz_xml["json"])) > 0 )
		{
			header( "Content-Type:".$mz_content_type);
			header( "Content-Disposition:attachment;filename=".$mz_filename);
			echo $mz_xml["json"];
		}
		else {
			$mz_xml = "No cite file found.";
		}
	}
	else {
		echo $mz_xml;
	}
?>
