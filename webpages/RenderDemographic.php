<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-09-03

require_once('StaffCommonCode.php');

function var_error_log( $object=null ){
    ob_start();                    // start buffer capture
    var_dump( $object );           // dump the values
    $contents = ob_get_contents(); // put the buffer into a variable
    ob_end_clean();                // end capture
    error_log( $contents );        // log contents of the result of var_dump( $object )
}

function render_demographic() {
	$demo = json_decode(getString("demographic"));
	$options = json_decode(getString("demographicOptions"));

    // Start of display portion
	$paramArray = array();

	$paramArray["name"] = property_exists($demo, "shortname") ? $demo->shortname : "";
	$paramArray["prompt"] = property_exists($demo, "prompt") ? $demo->prompt : "";
	$paramArray["hover"] = property_exists($demo, "hover") ? $demo->hover : "";
	$paramArray["typeid"] = (int) $demo->typeid;
	$paramArray["typename"] = $demo->typename;
    $paramArray["required"] = property_exists($demo, "required") ? $demo->required : 1;
    $paramArray["ascending"] = property_exists($demo, "ascending") ? $demo->ascending : 1;
	$paramArray["min"] = property_exists($demo, "min_value") ? ($demo->min_value != "" ? $demo->min_value : 0) : 0;
    $paramArray["max"] = property_exists($demo, "max_value") ? ($demo->max_value != "" ? $demo->max_value : 8192) : 8192;
	$paramArray["size"] = min(80, $paramArray["max"]);
    $paramArray["rows"] = $paramArray["max"] > 512 ? 8 : 4;

	var_error_log($paramArray);

	switch ($paramArray["typename"]) {
        case "number":
            $paramArray["size"] = max(2, (int) (1 + log10($paramArray["max"])));
			RenderXSLT('RenderDemographicOpenend.xsl', $paramArray);
			break;
        case "openend":
			RenderXSLT('RenderDemographicOpenend.xsl', $paramArray);
			break;
        case "text":
			RenderXSLT('RenderDemographicTextarea.xsl', $paramArray);
			break;
        default:
			echo $paramArray["typename"] . " not yet implimented.";
    }
}

// Start here.  Should be AJAX requests only
$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "") {
    exit();
}

switch ($ajax_request_action) {
    case "renderdemograhpic":
        render_demographic();
        break;

    default:
        exit();
}
?>