#!/usr/local/bin/php -q
<?php
//This page is intended to be hit from a cron job only.
//Need to add some code to prevent it from being accessed any other way, but leave it exposed for now for testing.
error_reporting(E_ERROR);
require_once('webpages/konOpas_func.php');
$results = retrieveKonOpasData();
if ($results["message_error"]) {
		error_log("konOpas.php: ".$results["message_error"]);
		exit(1);
		}
	else if ($results["json"]) {
		$resultsFile = fopen("webpages/konOpasData.jsonp","wb");
		if ($resultsFile === FALSE) {
			error_log("konOpas.php: Can't open webpages/konOpasData.jsonp for writing.");
			exit(1);
			}
		if (fwrite($resultsFile, $results["json"]) === FALSE) {
			error_log("konOpas.php: Error writing to webpages/konOpasData.jsonp.");
			exit(1);
			}
		exit();
		}
	else {
		error_log("konOpas.php: retrieveKonOpasData() did not return expected result or error indicator.");
		exit(1);
		}
?>