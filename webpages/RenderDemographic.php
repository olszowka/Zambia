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

// Function ArrayToXML()
// returns an XMLDoc as if from a query with the contents of the array
//
function ArrayToXML($queryname, $array, $xml = null) {
    if ($xml == null) {
        $xml = new DomDocument("1.0", "UTF-8");
        $doc = $xml -> createElement("doc");
        $doc = $xml -> appendChild($doc);
    } else {
        //error_log($xml->saveXML());
        $doc = $xml -> getElementsByTagName("doc")[0];
    }
    $queryNode = $xml -> createElement("query");
    $queryNode = $doc -> appendChild($queryNode);
    $queryNode->setAttribute("queryname", $queryname);
    foreach($array as $element) {
        $rowNode = $xml->createElement("row");
        $rowNode = $queryNode->appendChild($rowNode);
        $rowNode->setAttribute("value", $element);
    }
    // echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $permissionSetXML->saveXML(), "i"));
    return $xml;
}

function JSONtoXML($queryname, $json, $xml = null) {
    if ($xml == null) {
        $xml = new DomDocument("1.0", "UTF-8");
        $doc = $xml -> createElement("doc");
        $doc = $xml -> appendChild($doc);
    } else {
        //error_log($xml->saveXML());
        $doc = $xml -> getElementsByTagName("doc")[0];
    }
    $queryNode = $xml -> createElement("query");
    $queryNode = $doc -> appendChild($queryNode);
    $queryNode->setAttribute("queryname", $queryname);
    foreach($json as $element) {
        $rowNode = $xml->createElement("row");
        $rowNode = $queryNode->appendChild($rowNode);
        foreach($element as $key => $value) {
            $rowNode->setAttribute($key, $value);
        }
    }
    // echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $permissionSetXML->saveXML(), "i"));
    return $xml;
}

function render_demographic() {
    //error_log("\n------------------\nstart render_demographic:");
    $demographic = getString("demographic");
    //error_log("demographic encoded: " . $demographic);
    $demographic = base64_decode($demographic);
    //error_log("demographic decoded: " . $demographic);

	$demo = json_decode($demographic);
    //error_log("\ndemo:");
    //var_error_log($demo);

    $options = null;
    $demographicOptions = getString("options");
    if ($demographicOptions) {
        $demographicOptions = base64_decode($demographicOptions);
        error_log("demographicoptions: ". $demographicOptions);
        $useoptatob = true;
        if (mb_substr($demographicOptions, 0, 7) == "nobtoa:") {
            $useoptatob = false;
            $demographicOptions = mb_substr($demographicOptions, 7);
        }
	    $options = json_decode($demographicOptions);
        error_log("\n\nBefore foreach\n");
        var_error_log($options);
        if ($useoptatob) {
            foreach ($options as $option) {
                $option->value = base64_decode($option->value);
                $option->optionshort = base64_decode($option->optionshort);
                $option->hover = base64_decode($option->hover);
            }
        }
        error_log("\n\After foreach\n");
        var_error_log($options);
    }

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
    $optxml = null;
    if ($options) {
        if ($paramArray["ascending"] == 0 && $paramArray["typename"] != "monthyear") {
            $options = array_reverse($options);
        }
        $optxml = JSONtoXML('options', $options);
        //error_log($optxml->saveXML());
    }

	//var_error_log($paramArray);

	switch ($paramArray["typename"]) {
        case "number":
			RenderXSLT('RenderDemographicNumber.xsl', $paramArray);
			break;
        case "numberselect":
            // build xml array from begin to end
            $selectarr = [];
            if ($paramArray["ascending"] == 1) {
                $next = $paramArray["min"];
                $end = $paramArray["max"];
                while ($next <= $end) {
                    $selectarr[] = $next;
                    $next = $next + 1;
                }
            }
            else {
                $next = $paramArray["max"];
                $end = $paramArray["min"];
                while ($next >= $end) {
                    $selectarr[] = $next;
                    $next = $next - 1;
                }
            }
            //var_error_log($selectarr);
            $optxml = ArrayToXML("loop", $selectarr);
            //error_log($optxml->saveXML());
			RenderXSLT('RenderDemographicNumberSelect.xsl', $paramArray, $optxml);
			break;
        case "monthyear":
            // build xml array from begin to end
            $selectarr = [];
            if ($paramArray["ascending"] == 1) {
                $next = $paramArray["min"];
                $end = $paramArray["max"];
                while ($next <= $end) {
                    $selectarr[] = $next;
                    $next = $next + 1;
                }
            }
            else {
                $next = $paramArray["max"];
                $end = $paramArray["min"];
                while ($next >= $end) {
                    $selectarr[] = $next;
                    $next = $next - 1;
                }
            }
            //var_error_log($selectarr);
            $optxml = ArrayToXML("year", $selectarr, $optxml);
            //error_log($optxml->saveXML());
            RenderXSLT('RenderDemographicMonthyear.xsl', $paramArray, $optxml);
            break;
        case "monthnum":
        case "monthabv":
        case "states":
            RenderXSLT('RenderDemographicSelect.xsl', $paramArray, $optxml);
            break;
        case "openend":
			RenderXSLT('RenderDemographicOpenend.xsl', $paramArray);
			break;
        case "text":
			RenderXSLT('RenderDemographicTextarea.xsl', $paramArray);
			break;
        case "heading":
            RenderXSLT('RenderDemographicHeader.xsl', $paramArray);
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