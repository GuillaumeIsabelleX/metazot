<?php

require('../../../../../wp-load.php');
define('WP_USE_THEMES', false);

// Include Request Functionality
require('request.class.php');
require('request.functions.php');
require('libzotero.php');


// Content prep
$mz_xml = false;

// Key
if (isset($_GET['key']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['key']))
  $mz_item_key = trim(urldecode($_GET['key']));
else
  $mz_xml = "No key provided.";

// Api User ID
if (isset($_GET['api_user_id']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['api_user_id']))
  $mz_api_user_id = trim(urldecode($_GET['api_user_id']));
else
  $mz_xml = "No API User ID provided.";

if ($mz_xml === false)
{
  // Access WordPress db
  global $wpdb;
  
  // Get account
  $mz_account = mz_get_account ($wpdb, $mz_api_user_id);
  $mz_url = "https://api.zotero.org/".$mz_account[0]->account_type."/".$mz_api_user_id."/items/".$mz_item_key;
  $mz_version_url = $mz_url;

  $verch = curl_init();
  //headers
  $verhttpHeaders = array();
  //set api version - allowed to be overridden by passed in value
  if(!isset($verheaders['Zotero-API-Version'])){
      $verheaders['Zotero-API-Version'] = ZOTERO_API_VERSION;
  }

  if(!isset($verheaders['Zotero-API-Key'])){
    $verheaders['Zotero-API-Key'] = $mz_account[0]->public_key;
    }

  if(!isset($verheaders['Content-Type'])){
    $verheaders['Content-Type'] = 'application/json';
}
  
  foreach($verheaders as $key=>$val){
      $verhttpHeaders[] = "$key: $val";
  }

  // Set query data here with the URL
  curl_setopt($verch, CURLOPT_FRESH_CONNECT, true);
  curl_setopt($verch, CURLOPT_URL, $mz_version_url); 
  curl_setopt($verch, CURLOPT_HEADER, true);
  curl_setopt($verch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($verch, CURLINFO_HEADER_OUT, true);
  curl_setopt($verch, CURLOPT_HTTPHEADER, $verhttpHeaders);
  curl_setopt($verch, CURLOPT_MAXREDIRS, 3);
  curl_setopt($verch, CURLOPT_TIMEOUT, 3);
  
  $verresponseBody = curl_exec($verch);
  $verrespheaders = Zotero\HttpResponse::extractHeaders($verresponseBody);
  if(!$verresponseBody){
      echo $verresponseBody->getStatus() . "\n";
      echo $verresponseBody->getBody() . "\n";
      die("Error creating attachment item\n\n");
  }
  echo '<pre>'; print_r($verrespheaders); echo '</pre>';
  $mz_version = $verrespheaders['last-modified-version'];
  $ch = curl_init();
  $itemBody = $_POST;
  $library = new Zotero\Library($mz_account[0]->account_type, $mz_api_user_id, '', $mz_account[0]->public_key);
  echo '<pre>'; print_r($itemBody); echo '</pre>';
  //add child attachment
  //get attachment template
  echo "updating item\n";
  try{

  unset($itemBody['submit']);
  $requestData = json_encode($itemBody);
  $ch = curl_init();
  $httpHeaders = array();
  //set api version - allowed to be overridden by passed in value
  if(!isset($headers['Zotero-API-Version'])){
      $headers['Zotero-API-Version'] = ZOTERO_API_VERSION;
  }

  if(!isset($headers['Zotero-API-Key'])){
    $headers['Zotero-API-Key'] = $mz_account[0]->public_key;
    }

  if(!isset($headers['Content-Type'])){
    $headers['Content-Type'] = 'application/json';
}

if(!isset($headers['If-Unmodified-Since-Version'])){
  $headers['If-Unmodified-Since-Version'] = $mz_version;
}

if(!isset($headers['Expect'])){
  $headers['Expect'] = '';
}
  
  foreach($headers as $key=>$val){
      $httpHeaders[] = "$key: $val";
  }

  curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
  curl_setopt($ch, CURLOPT_URL, $mz_url );
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
  curl_setopt($ch, CURLOPT_POSTFIELDS,$requestData);
  $responseBody = curl_exec($ch);

  if(!$responseBody){
      echo $responseBody->getStatus() . "\n";
      echo $responseBody->getBody() . "\n";
      die("Error creating attachment item\n\n");
  }
}
  catch(Exception $e){
      echo $e->getMessage();
      $lastResponse = $library->getLastResponse();
      echo $lastResponse->getStatus() . "\n";
      echo $lastResponse->getRawBody() . "\n";
  }
}
else {
  echo $mz_xml;
}	

?>