<?php
function convert_timestamp_to_timeindex($timestamp,$start) {
	// $timestamp in hh:mm:ss or hhh:mm:ss from start of con
	// start = 1 if starttime; 0 if endtime
	global $timesXPath;
	$hour = 0 + substr($timestamp,0,strlen($timestamp)-6); // get 1st 2 if hh:mm:ss or 1st 3 if hhh:mm:ss
	$minute = substr($timestamp,strlen($timestamp)-5,2); // handle hh:mm:ss or hhh:mm:ss
	//echo($hour)."&nbsp;&nbsp;";
	if (($hour%24)>=DAY_CUTOFF_HOUR) {
			$next_day = 0;
			$day = 1 + floor($hour/24);
			}
		else {
			$next_day = 1;
			$day = 0 + floor($hour/24);
			}
	$hour %= 24;
	$searchTime = (($hour<10)?"0":"") . $hour . ":" . $minute . ":00";
	$xPathQuery = "string(query/row[@next_day='";
	$xPathQuery .= ($next_day==1)?"1":"0";
	$xPathQuery .= "' and @" . (($start)?"avail_start":"avail_end") . "='1' and ";
	$xPathQuery .= "@timevalue = '" . $searchTime . "']/@timeid)";
	//echo($xPathQuery)."<BR>";
	$timesIndex = $timesXPath->evaluate($xPathQuery);
	if (strlen($timesIndex)==0)
		$timesIndex="0";
	return(array("day" => $day, "hour" => $timesIndex));
}

function retrieve_timesXML() {
	global $timesXML, $timesXPath, $variablesNode;
	$query["times"] = "SELECT timeid, DATE_FORMAT(timevalue,'%T') AS timevalue, timedisplay, next_day, avail_start, avail_end FROM Times ";
	$query["times"] .= "WHERE avail_start = 1 or avail_end = 1";
	if (!$timesXML=mysql_query_XML($query)) {
        RenderError($title,$message_error);
        exit();
        }
	$timesXPath = new DOMXPath($timesXML);
	$variablesNode = $timesXML->createElement("variables");
	$docNode = $timesXML->getElementsByTagName("doc")->item(0);
	$variablesNode = $docNode->appendChild($variablesNode);
}
?>
