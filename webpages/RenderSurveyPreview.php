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

function ObjecttoXML($queryname, $json, $xml = null) {
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

function render_question() {
    //error_log("\n------------------\nstart render_question:");
    $question = getString("question");
    //error_log("question encoded: " . $question);
    $question = base64_decode($question);
    //error_log("question decoded: " . $question);

	$quest = json_decode($question);
    //error_log("\nquest:");
    //var_error_log($quest);

    $options = null;
    $questionOptions = getString("options");
    if ($questionOptions) {
        $questionOptions = base64_decode($questionOptions);
        //error_log("questionOptions: ". $questionOptions);
        $useoptatob = true;
        if (mb_substr($questionOptions, 0, 7) == "nobtoa:") {
            $useoptatob = false;
            $questionOptions = mb_substr($questionOptions, 7);
        }
	    $options = json_decode($questionOptions);
        //error_log("\n\nBefore foreach\n");
        //var_error_log($options);
        if ($useoptatob) {
            foreach ($options as $option) {
                $option->value = base64_decode($option->value);
                $option->optionshort = base64_decode($option->optionshort);
                $option->optionhover = base64_decode($option->optionhover);
            }
        }
        //error_log("\n\After foreach\n");
        //var_error_log($options);
    }

    // Start of display portion
	$paramArray = array();

	$paramArray["name"] = property_exists($quest, "shortname") ? $quest->shortname : "";
	$paramArray["prompt"] = property_exists($quest, "prompt") ? $quest->prompt : "";
	$paramArray["hover"] = property_exists($quest, "hover") ? $quest->hover : "";
    $paramArray["typeid"] = (int) $quest->typeid;
	$paramArray["typename"] = $quest->typename;
    $paramArray["required"] = property_exists($quest, "required") ? $quest->required : 1;
    $paramArray["ascending"] = property_exists($quest, "ascending") ? $quest->ascending : 1;
    $paramArray["display_only"] = property_exists($quest, "display_only") ? $quest->display_only : 0;
	$paramArray["min"] = property_exists($quest, "min_value") ? ($quest->min_value != "" ? $quest->min_value : 0) : 0;
    $paramArray["max"] = property_exists($quest, "max_value") ? ($quest->max_value != "" ? $quest->max_value : 8192) : 8192;
	$paramArray["size"] = min(80, $paramArray["max"]);
    $paramArray["rows"] = $paramArray["max"] > 512 ? 8 : 4;
    $optxml = null;
    if ($options) {
        if ($paramArray["ascending"] == 0 && $paramArray["typename"] != "monthyear") {
            $options = array_reverse($options);
        }
        $optxml = ObjecttoXML('options', $options);
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
        case "country":
        case "single-pulldown":
        case "multi-select list":
            RenderXSLT('RenderDemographicSelect.xsl', $paramArray, $optxml);
            break;
        case "multi-checkbox list":
            RenderXSLT('RenderDemographicCheckboxList.xsl', $paramArray, $optxml);
            break;
        case "multi-display":
            RenderXSLT('RenderDemographicMultiDisplay.xsl', $paramArray, $optxml);
            break;
        case "single-radio":
            RenderXSLT('RenderDemographicRadio.xsl', $paramArray, $optxml);
            break;
        case "openend":
			RenderXSLT('RenderDemographicOpenend.xsl', $paramArray);
			break;
        case "text":
        case "html-text";
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
    case "renderquestion":
        render_question();
        break;

    default:
        exit();
}
?>