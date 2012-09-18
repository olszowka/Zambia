<?php
require_once('db_functions.php');
require_once('data_functions.php');
require_once('StaffCommonCode.php');
require_once('SubmitMaintainRoom.php');

function retrieveRoomsTable() {
	global $link,$message_error;
	if (!isset($_POST["roomsToDisplayArray"]))
		exit();
	$roomsToDisplayArray = $_POST["roomsToDisplayArray"];
	$roomsToDisplayList = "";
	if (!$roomsToDisplayArray)
		exit();
	foreach ($roomsToDisplayArray as $i => $id)
		$roomsToDisplayList .= intval($id).",";
	$roomsToDisplayList = substr($roomsToDisplayList,0,-1); //drop extra trailing comma
	$queryArray["rooms"] =<<<EOD
SELECT R.roomid, R.roomname FROM Rooms R WHERE R.roomid IN ($roomsToDisplayList)
	ORDER BY R.display_order
EOD;
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($title,$message_error);
        exit();
        }
	$xmlstr = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
	<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
		<xsl:output omit-xml-declaration="yes" />
		<xsl:template match="/">
			<xsl:apply-templates match="doc/query[@queryName='rooms']/row" />
		</xsl:template>
		<xsl:template match="/doc/query[@queryName='rooms']/row">
			<th class="schedulerGridRoom" roomid="{@roomid}"><xsl:value-of select="@roomname" /></th>
		</xsl:template>
	</xsl:stylesheet>
EOD;
	$xsl = new DomDocument;
	$xsl->loadXML($xmlstr);
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$roomsHtml = $xslt->transformToXML($resultXML);
	$htmlTimesArray = getScheduleTimesArray($roomsToDisplayList);
	$query = <<<EOD
SELECT
		SCH.scheduleid, SCH.roomid, SCH.starttime, ADDTIME(SCH.starttime, S.duration) AS endtime,
	 	S.sessionid, S.title, S.progguiddesc, TR.trackname, TY.typename, D.divisionname, S.duration
	FROM
			 Schedule SCH
		JOIN Sessions S USING (sessionid)
		JOIN Tracks TR USING (trackid)
		JOIN Types TY USING (typeid)
		JOIN Divisions D USING (divisionid)
	WHERE
		SCH.roomid IN ($roomsToDisplayList)
	ORDER BY
		SCH.roomid, SCH.starttime, endtime DESC;
EOD;
	$result = mysql_query_with_error_handling($query);
	$scheduleArray = array();
	foreach ($roomsToDisplayArray as $roomIndex => $roomId)
		$scheduleArray[$roomId] = array();
	while($row = mysql_fetch_assoc($result))
		{
		list($startTimeHour, $startTimeMin, $foo) = sscanf($row["starttime"],"%d:%d:%d");
		list($endTimeHour, $endTimeMin, $foo) = sscanf($row["endtime"],"%d:%d:%d");
		$scheduleArray[$row["roomid"]][]=array(
			"scheduleid" => $row["scheduleid"],
			"starttime" => $row["starttime"],
			"endtime" => $row["endtime"],
			"startTimeUnits" => convertStartTimeToUnits($startTimeHour, $startTimeMin),
			"endTimeUnits" => convertEndTimeToUnits($endTimeHour,$endTimeMin),
			"sessionid" => $row["sessionid"],
			"title" => $row["title"],
			"progguiddesc" => $row["progguiddesc"],
			"trackname" => $row["trackname"],
			"typename" => $row["typename"],
			"divisionname" => $row["divisionname"],
			"duration" => $row["duration"]);
		}
	$roomsHTMLArray = array();
	foreach ($roomsToDisplayArray as $roomIndex => $roomId)
		{
		$roomsHTMLArray[$roomIndex] = getHTMLforRoom($roomId, $htmlTimesArray, $scheduleArray);
		}
	echo "<table class=\"schedulerGrid\">\n";
	echo "<tr>";
	echo $htmlTimesArray[0]["html"];
	echo $roomsHtml;
	echo "</tr>\n";
	for($i = 1; $i < count($htmlTimesArray); $i++) {
		echo "<tr>";
		echo $htmlTimesArray[$i]["html"];
		foreach ($roomsToDisplayArray as $roomIndex => $roomId)
			{
			if (isset($roomsHTMLArray[$roomIndex][$i]))
			 	echo $roomsHTMLArray[$roomIndex][$i];
			}
		echo "</tr>\n";
		}
	echo "</table>\n";
}

function getScheduleTimesArray($roomsToDisplayList)
	{
	global $message_error, $link, $daymap;
	$htmlTimesArray = array();
	$nextStartTimeUnits = 0;
	list($firstDayStartTimeHour,$firstDayStartTimeMin) = sscanf(FIRST_DAY_START_TIME,"%d:%d");
	$firstDayStartTimeUnits = convertStartTimeToUnits($firstDayStartTimeHour, $firstDayStartTimeMin);
	list($otherDayEndTimeHour,$otherDayEndTimeMin) = sscanf(OTHER_DAY_STOP_TIME,"%d:%d");
	$otherDayEndTimeUnits = convertStartTimeToUnits($otherDayEndTimeHour, $otherDayEndTimeMin);
	list($otherDayStartTimeHour,$otherDayStartTimeMin) = sscanf(OTHER_DAY_START_TIME,"%d:%d");
	$otherDayStartTimeUnits = convertStartTimeToUnits($otherDayStartTimeHour, $otherDayStartTimeMin);
	list($lastDayEndTimeHour,$lastDayEndTimeMin) = sscanf(LAST_DAY_STOP_TIME,"%d:%d");
	$lastDayEndTimeUnits = convertStartTimeToUnits($lastDayEndTimeHour, $lastDayEndTimeMin);
	$cutoffHour = DAY_CUTOFF_HOUR;
	$cutoffUnits = convertStartTimeToUnits($cutoffHour, 0);
	$nextStartTimeHour = 0;
	$nextStartTimeMin = 0;
	$gap = false;
	for ($day = 1; $day <= CON_NUM_DAYS; $day++)
		{
		$dayName = $daymap["long"][$day];
		if ($day < CON_NUM_DAYS)
			$nextDayName = $daymap["long"][$day + 1];
		$thisCutoffUnits = $cutoffUnits + $day * 48; // always next day even on 1st day
		$thisCutoffTimeStr = convertUnitsToTimeStr($thisCutoffUnits);
		if ($day == 1)
				{
				//calculate beginning for first day
				// mode: -1 is gap; 0, 1, & 2 are rows of standard block
				$htmlTimesArray[] = array("hr" => 0, "min" => 0, "mode" => -1, "units" => 0, "html" => "<th>".$dayName."</th>");

				$query = "SELECT MIN(SCH.starttime) AS starttime FROM Schedule SCH WHERE roomid IN ($roomsToDisplayList);";
				$result = mysql_query_with_error_handling($query);
			    if (!$result)
					{
			        RenderErrorAjax($message_error);
			        exit();
			        }
				if (mysql_num_rows($result) == 0)
						$startTimeUnits = $firstDayStartTimeUnits;
					else
						{
						list($startTimeHour,$startTimeMin,$foo) = sscanf(mysql_result($result, 0), "%d:%d:%d");
						$startTimeUnits = convertStartTimeToUnits($startTimeHour, $startTimeMin);
						if ($startTimeHour == null || $firstDayStartTimeUnits < $startTimeUnits)
							$startTimeUnits = $firstDayStartTimeUnits;
						}
				}
			else
				{
				// calculate beginning for other than first day
				$startTimeUnits = $nextStartTimeUnits;
				if ($gap)
					$htmlTimesArray[] = array("hr" => 0, "min" => 0, "mode" => -1, "units" => 0, "html" => "<td class=\"gap\">".$dayName."</td>");
				}
		// found beginning; now find ending
		if ($day<CON_NUM_DAYS)
				{
				$previousCutoffUnits = $cutoffUnits + ($day - 1) * 48;
				$previousCutoffTimeStr = convertUnitsToTimeStr($previousCutoffUnits);
				$query =<<<EOD
SELECT MAX(ADDTIME(SCH.starttime, S.duration)) AS endtime
	FROM Schedule SCH JOIN Sessions S USING(sessionid)
	WHERE
			SCH.roomid IN ($roomsToDisplayList)
		AND ADDTIME(SCH.starttime, S.duration) < '$thisCutoffTimeStr'
		AND SCH.starttime >= '$previousCutoffTimeStr';
EOD;
				$result = mysql_query_with_error_handling($query);
			    if (!$result)
					{
			        RenderErrorAjax($message_error);
			        exit();
			        }
				if (mysql_num_rows($result) == 0)
						$endTimeUnits = $otherDayEndTimeUnits + ($day - 1) * 48;
					else
						{
						list($endTimeHour,$endTimeMin,$foo) = sscanf(mysql_result($result, 0), "%d:%d:%d");
						$endTimeUnits = convertEndTimeToUnits($endTimeHour, $endTimeMin);
						if ($endTimeHour == null || $endTimeUnits < $otherDayEndTimeUnits + ($day - 1) * 48)
							$endTimeUnits = $otherDayEndTimeUnits + ($day - 1) * 48;
						}
				if ($endTimeUnits >= $thisCutoffUnits)
						{
						$gap = false;
						$endTimeUnits = $thisCutoffUnits - 1;
						$nextStartTimeUnits = $thisCutoffUnits;
						}
					else
						{
						$query =<<<EOD
SELECT MIN(SCH.starttime) AS starttime
	FROM Schedule SCH JOIN Sessions S USING(sessionid)
	WHERE
			SCH.roomid IN ($roomsToDisplayList)
		AND ADDTIME(SCH.starttime, S.duration) >= '$thisCutoffTimeStr';
EOD;
						$result = mysql_query_with_error_handling($query);
						if (!$result)
							{
						    RenderErrorAjax($message_error);
						    exit();
						    }
						if (mysql_num_rows($result) == 0)
								{
								$gap = true;
								$nextStartTimeUnits = $otherDayStartTimeUnits + $day * 48;
								}
							else
								{
								list($nextStartTimeHour,$nextStartTimeMin,$foo) = sscanf(mysql_result($result, 0), "%d:%d");
								$nextStartTimeUnits = convertStartTimeToUnits($nextStartTimeHour,$nextStartTimeMin);
								if ($nextStartTimeHour == null)
										{
										$gap = true;
										$nextStartTimeUnits = $otherDayStartTimeUnits + $day * 48;
										}
									elseif (($nextStartTimeUnits - $endTimeUnits) >= 2)
										{
										$gap = true;
										if ($nextStartTimeUnits > $otherDayStartTimeUnits + $day * 48)
											$nextStartTimeUnits = $otherDayStartTimeUnits + $day * 48;
										}
									else
										{
										$gap = false;
										$endTimeUnits = $thisCutoffUnits - 1;
										$nextStartTimeUnits = $thisCutoffUnits;
										}
								}
						}
				}	
			else
				{
				//finding end for last day now
				$query =<<<EOD
SELECT MAX(ADDTIME(SCH.starttime, S.duration)) AS endtime
	FROM Schedule SCH JOIN Sessions S USING(sessionid)
	WHERE SCH.roomid IN ($roomsToDisplayList);
EOD;
				$result = mysql_query_with_error_handling($query);
				if (!$result)
					{
				    RenderErrorAjax($message_error);
				    exit();
				    }
				if (mysql_num_rows($result) == 0)
						$endTimeUnits = $lastDayEndTimeUnits + ($day - 1) * 48;
					else
						{
						list($endTimeHour,$endTimeMin,$foo) = sscanf(mysql_result($result, 0), "%d:%d:%d");
						$endTimeUnits = convertEndTimeToUnits($endTimeHour, $endTimeMin);
						if ($endTimeHour == null || $endTimeUnits < $lastDayEndTimeUnits + ($day - 1) * 48)
							$endTimeUnits = $lastDayEndTimeUnits + ($day - 1) * 48;
						}
				}
		$nowInUnits = $startTimeUnits;
		$nowHour = 0;
		$nowMin = 0;
		if ($day == 1)
				$modeIndex = ($nowInUnits - $firstDayStartTimeUnits ) % 3;
			else
				$modeIndex = ($nowInUnits - $otherDayStartTimeUnits ) % 3;
		if ($modeIndex < 0)
			$modeIndex += 3;
		while ($nowInUnits <= $endTimeUnits)
			{
			if ($modeIndex > 2)
				$modeIndex = 0;
			list($nowHour, $nowMin) = convertUnitsToHourMin($nowInUnits);
			$htmlTimesArray[] = array("hr" => $nowHour, "min" => $nowMin, "units" => $nowInUnits, "mode" => $modeIndex);
			end($htmlTimesArray);
			$mykey = key($htmlTimesArray);
			if ($nowHour < ($day * 24))
					{
					$titleStr = $dayName;
					}
				else
					{
					$titleStr = $dayName." overnight into ".$nextDayName;
					}
			if ($nowMin == 0)
					{
					if ($nowHour%24==0)
							{
							$htmlTimesArray[$mykey]["html"] = "<td class=\"timeTop\" title=\"$titleStr\" mode=\"$modeIndex\">12:00a</td>";
							}
						else if ($nowHour%24<12)
							{
							$htmlTimesArray[$mykey]["html"] = "<td class=\"timeTop\" title=\"$titleStr\" mode=\"$modeIndex\">".($nowHour%24).":00a</td>";
							}
						else if ($nowHour%24==12)
							{
							$htmlTimesArray[$mykey]["html"] = "<td class=\"timeTop\" title=\"$titleStr\" mode=\"$modeIndex\">12:00p</td>";
							}
						else
							{
							$htmlTimesArray[$mykey]["html"] = "<td class=\"timeTop\" title=\"$titleStr\" mode=\"$modeIndex\">".(($nowHour%24)-12).":00p</td>";
							}
					}
				else
					{
					$htmlTimesArray[$mykey]["html"] = "<td class=\"timeBottom\" title=\"$titleStr\" mode=\"$modeIndex\">&nbsp;</td>";
					}
			$nowInUnits++;
			$modeIndex++;
			}
		//break;
		}
	return $htmlTimesArray;
	}

function getHTMLforRoom($roomId, $htmlTimesArray, $scheduleArray)
	{
	global $thisRoomSchedArray, $key, $thisSlotEndUnits, $blockHTML, $thisSlotLength, $thisSlotBeginUnits;
	$roomHTMLColumn = array();
	$schedLength = count($htmlTimesArray);
	$thisRoomSchedArray = $scheduleArray[$roomId];
	reset($thisRoomSchedArray);
	$key = key($thisRoomSchedArray);
	$i = 1;
	do //length counted from 0, but we're skipping 1st one (numbered 0)
		{
		if ($htmlTimesArray[$i]["mode"] == -1) // gap
			{
			$roomHTMLColumn[$i]="<td class=\"gap schedulerGridRoom\">&nbsp;</td>";
			$i++;
			continue;
			}
		$thisSlotBeginUnits = $htmlTimesArray[$i]["units"];
		switch($htmlTimesArray[$i]["mode"])
			{
			case "0":
				if (!isset($htmlTimesArray[$i+1]["mode"]) || $htmlTimesArray[$i+1]["mode"]==-1)
						{
						$thisSlotEndUnits = $thisSlotBeginUnits + 1;
						$thisSlotLength = 1;
						}
					elseif (!isset($htmlTimesArray[$i+2]["mode"]) || $htmlTimesArray[$i+2]["mode"]==-1)
						{
						$thisSlotEndUnits = $thisSlotBeginUnits + 2;
						$thisSlotLength = 2;	
						}
					else
						{
						$thisSlotEndUnits = $thisSlotBeginUnits + 3;
						$thisSlotLength = 3;	
						}
				break;
			case "1":
				if (!isset($htmlTimesArray[$i+1]["mode"]) || $htmlTimesArray[$i+1]["mode"]==-1)
						{
						$thisSlotEndUnits = $thisSlotBeginUnits + 1;
						$thisSlotLength = 1;	
						}
					else
						{
						$thisSlotEndUnits = $thisSlotBeginUnits + 2;
						$thisSlotLength = 2;	
						}
				break;
			case "2":
				$thisSlotEndUnits = $thisSlotBeginUnits + 1;
				$thisSlotLength = 1;
				break;
			}
		// determined slot
		doABlock($roomId);
		$roomHTMLColumn[$i] = $blockHTML;
		$i += $thisSlotLength;
		} while ($i < $schedLength);
	return $roomHTMLColumn;
	}

function doABlock($roomId)
	{
	global $thisRoomSchedArray, $key, $thisSlotEndUnits, $blockHTML, $thisSlotLength, $thisSlotBeginUnits, $thisSlot;
	if (!isset($thisRoomSchedArray[$key]) || $thisRoomSchedArray[$key]["startTimeUnits"] >= $thisSlotEndUnits)
		// room is empty
		{
		$blockHTML = emptySchedBlock($roomId);
		return;
		}
	if ($thisRoomSchedArray[$key]["startTimeUnits"] > $thisSlotBeginUnits)
		// make empty slot before session start
		{
		$thisSlotEndUnits = $thisRoomSchedArray[$key]["startTimeUnits"];
		$thisSlotLength = $thisSlotEndUnits - $thisSlotBeginUnits;
		$blockHTML = emptySchedBlock($roomId);
		return;
		}
	if ($thisRoomSchedArray[$key]["endTimeUnits"] != $thisSlotEndUnits)
		// need to modify the slot -- shrink or stretch
		{
		$thisSlotEndUnits = $thisRoomSchedArray[$key]["endTimeUnits"];
		$thisSlotLength = $thisSlotEndUnits - $thisSlotBeginUnits;
		}
	if (!isset($thisRoomSchedArray[$key + 1]) || $thisRoomSchedArray[$key + 1]["startTimeUnits"] >= $thisSlotEndUnits)
		// only one item in the slot
		{
		// render a simple block with one session
		$blockHTML = "<td class=\"schedulerGridRoom schedulerGridSlot\"";
		if ($thisSlotLength > 1)
			$blockHTML .= " rowspan=\"$thisSlotLength\"";
		$blockHTML .= ">";
		$blockHTML .= "<div class=\"schedulerGridContainer\" style=\"height:".($thisSlotLength*18-2)."px;\">";
		$blockHTML .= "<div id=\"sessionBlockDIV_{$thisRoomSchedArray[$key]["sessionid"]}\" class=\"scheduledSessionBlock\" ";
		$blockHTML .=      "sessionid=\"{$thisRoomSchedArray[$key]["sessionid"]}\" scheduleid=\"{$thisRoomSchedArray[$key]["scheduleid"]}\" ";
		$blockHTML .=      "roomid=\"$roomId\" startTimeUnits=\"$thisSlotBeginUnits\" endTimeUnits=\"$thisSlotEndUnits\" ";
		$blockHTML .=      "startTime=\"{$thisRoomSchedArray[$key]["starttime"]}\" endTime=\"{$thisRoomSchedArray[$key]["endtime"]}\" ";
		$blockHTML .=      "duration=\"{$thisRoomSchedArray[$key]["duration"]}\" >";
		$blockHTML .= "<div class=\"sessionBlockTitleRow\">";
		$blockHTML .= "<i class=\"icon-info-sign getSessionInfoP\"></i>";	
		//$blockHTML .= "<div class=\"ui-icon ui-icon-info getSessionInfoP\"></div>";
		$blockHTML .= "<div class=\"sessionBlockTitle\">{$thisRoomSchedArray[$key]["title"]}</div>";
		$blockHTML .= "</div>";
		$blockHTML .= "<div>";
		$blockHTML .= "<span class=\"sessionBlockId\">{$thisRoomSchedArray[$key]["sessionid"]}</span>";
		$blockHTML .= "<span class=\"sessionBlockDivis\">{$thisRoomSchedArray[$key]["divisionname"]}</span>";
		$blockHTML .= "</div>";
		$blockHTML .= "<div>";
		$blockHTML .= "<span class=\"sessionBlockType\">{$thisRoomSchedArray[$key]["typename"]}</span>";
		$blockHTML .= "<span class=\"sessionBlockTrack\">{$thisRoomSchedArray[$key]["trackname"]}</span>";
		$blockHTML .= "</div>"; // last row of info
		$blockHTML .= "</div>"; // session block
		$blockHTML .= "</div>"; // container
		$blockHTML .= "</td>";
		$key++;
		return;
		}
	// need	to find all the sessions in the collection before a time border across which none extend
	$i = 1;
	while (isset($thisRoomSchedArray[$key + $i]))
		{
		if ($thisRoomSchedArray[$key + $i]["startTimeUnits"] >= $thisSlotEndUnits)
			// found a session outside the "cluster"
			{
			$i--;
			break;
			}
		if ($thisRoomSchedArray[$key + $i]["endTimeUnits"] >= $thisSlotEndUnits)
			{
			$thisSlotEndUnits = $thisRoomSchedArray[$key + $i]["endTimeUnits"];
			$thisSlotLength = $thisSlotEndUnits - $thisSlotBeginUnits;
			}
		$i++;
		}
	if (!isset($thisRoomSchedArray[$key + $i]))
		//for now it is important to point to last existing key
		$i--;
	// having found the cluster, need to determine whether any slots have more than two sessions occupying them.
	$slotCounter = array();
	for ($thisSlot = $thisSlotBeginUnits; $thisSlot < $thisSlotEndUnits; $thisSlot++)
		{
		$slotCounter[$thisSlot] = 0;
		for ($thisKey = $key; $thisKey <= $key + $i; $thisKey++)
			{
			if ($thisSlot >= $thisRoomSchedArray[$thisKey]["startTimeUnits"] &&
				$thisSlot < $thisRoomSchedArray[$thisKey]["endTimeUnits"])
				{
				$slotCounter[$thisSlot]++;
				if ($slotCounter[$thisSlot] > 2)
					{
					renderComplicatedBlock($roomId);
					$key += $i + 1;	
					return;	
					}
				}
			}
		}
	// having found the cluster, need to render it and reset the key
	$blockHTML = "<td class=\"schedulerGridRoom schedulerGridSlot\"";
	if ($thisSlotLength > 1)
		$blockHTML .= " rowspan=\"$thisSlotLength\"";
	$blockHTML .= ">";
	$blockHTML .= "<div class=\"scheduleGridCompoundDIV\" style=\"height:".floor($thisSlotLength*19.75-1.5)."px;\" roomid=\"$roomId\" ";
	$blockHTML .=      "startTimeUnits=\"$thisSlotBeginUnits\" endTimeUnits=\"$thisSlotEndUnits\">";
	$blockHTML .= "<table class=\"scheduleGridCompTAB\">";
	$AScheduledUpTo = $thisSlotBeginUnits;
	$BScheduledUpTo = $thisSlotBeginUnits;
	$thisKey = $key;
	for ($thisSlot = $thisSlotBeginUnits; $thisSlot < $thisSlotEndUnits; $thisSlot++)
		{
		$blockHTML .= "<tr class=\"compoundTR\">";
		doACompSlot($AScheduledUpTo, $thisSlot, $thisKey, $i, $roomId);
        doACompSlot($BScheduledUpTo, $thisSlot, $thisKey, $i, $roomId);
        $blockHTML .= "</tr>";
		}
	$blockHTML .= "</table>";
	$blockHTML .= "</div></td>";
	$key += $i + 1;
	}

function doACompSlot(&$ScheduledUpTo, $thisSlot, &$thisKey, $i, $roomId) 
	{
	global $key, $thisSlotBeginUnits, $thisSlotEndUnits, $blockHTML, $thisRoomSchedArray;
	if ($ScheduledUpTo == $thisSlot)
		{
		if ($thisKey > $key + $i)
				// no more sessions to put into the compound block; just put in blanks through the end
				{
				$thisCBlockLength = $thisSlotEndUnits - $thisSlot;
				$blockHTML .= "<td";
				if ($thisCBlockLength > 1)
					$blockHTML .= " rowspan=\"$thisCBlockLength\"";
				$blockHTML .= " class=\"compoundTD\">";
				$blockHTML .= "<div class=\"scheduleGridCompoundEmptyDIV\" style=\"height:" . floor($thisCBlockLength * 18.75 - 0.5) . "px\" ";
				$blockHTML .=      " roomid=\"$roomId\" startTimeUnits=\"$thisSlot\">&nbsp;</div>";
				$blockHTML .= "</td>";
				$ScheduledUpTo = $thisSlotEndUnits;
				}
				// can assume we are not done with blocks at this point
			else if ($thisRoomSchedArray[$thisKey]["startTimeUnits"] == $thisSlot)
				// put in a real session
				{
				$thisCBlockLength = $thisRoomSchedArray[$thisKey]["endTimeUnits"] - $thisRoomSchedArray[$thisKey]["startTimeUnits"];
				$ScheduledUpTo = $thisRoomSchedArray[$thisKey]["endTimeUnits"];
                $blockHTML .= "<td";
                if ($thisCBlockLength > 1)
                    $blockHTML .= " rowspan=\"$thisCBlockLength\"";
                $blockHTML .= " class=\"compoundTD\">";
				$blockHTML .= "<div class=\"scheduleGridCompoundSessContainer\" style=\"height:" . floor($thisCBlockLength * 18.75 - 0.5) . "px\">";
				$blockHTML .= "<div id=\"sessionBlockDIV_{$thisRoomSchedArray[$thisKey]["sessionid"]}\" class=\"scheduledSessionBlock\" ";
				$blockHTML .=     "sessionid=\"{$thisRoomSchedArray[$thisKey]["sessionid"]}\" ";
				$blockHTML .=     "scheduleid=\"{$thisRoomSchedArray[$thisKey]["scheduleid"]}\" ";
				$blockHTML .=     "roomid=\"$roomId\" startTimeUnits=\"{$thisRoomSchedArray[$thisKey]["startTimeUnits"]}\" ";
				$blockHTML .=     "endTimeUnits=\"{$thisRoomSchedArray[$thisKey]["endTimeUnits"]}\" ";
				$blockHTML .=     "startTime=\"{$thisRoomSchedArray[$thisKey]["starttime"]}\" ";
				$blockHTML .=     "endTime=\"{$thisRoomSchedArray[$thisKey]["endtime"]}\" duration=\"{$thisRoomSchedArray[$thisKey]["duration"]}\" >";
				$blockHTML .= "<div class=\"sessionBlockTitleRow\">";
				$blockHTML .= "<i class=\"icon-info-sign getSessionInfoP\"></i>";	
				//$blockHTML .= "<div class=\"ui-icon ui-icon-info getSessionInfoP\"></div>";
				$blockHTML .= "<div class=\"sessionBlockTitle\">{$thisRoomSchedArray[$thisKey]["title"]}</div>";
				$blockHTML .= "</div>";
				$blockHTML .= "<div>";
				$blockHTML .= "<span class=\"sessionBlockId\">{$thisRoomSchedArray[$thisKey]["sessionid"]}</span>";
				$blockHTML .= "<span class=\"sessionBlockDivis\">{$thisRoomSchedArray[$thisKey]["divisionname"]}</span>";
				$blockHTML .= "</div>";
				$blockHTML .= "<div>";
				$blockHTML .= "<span class=\"sessionBlockType\">{$thisRoomSchedArray[$thisKey]["typename"]}</span>";
				$blockHTML .= "<span class=\"sessionBlockTrack\">{$thisRoomSchedArray[$thisKey]["trackname"]}</span>";
				$blockHTML .= "</div>"; // last row of info
				$blockHTML .= "</div>"; // session block
				$blockHTML .= "</div>"; // container
				$blockHTML .= "</td>";
				$thisKey++;
				}
			else
				// put in a blank spot up to the next session
				// don't have to worry about "reserving" the session, the next
				// one will necessarily go here
				{
				$thisCBlockLength = $thisRoomSchedArray[$thisKey]["startTimeUnits"] - $thisSlot;
				$blockHTML .= "<td";
				if ($thisCBlockLength > 1)
					$blockHTML .= " rowspan=\"$thisCBlockLength\"";
				$blockHTML .= " class=\"compoundTD\">";
				$blockHTML .= "<div class=\"scheduleGridCompoundEmptyDIV\" style=\"height:" . floor($thisCBlockLength * 18.75 - 0.5) . "px\" ";
				$blockHTML .=      " roomid=\"$roomId\" startTimeUnits=\"$thisSlot\">&nbsp;</div>";
				$blockHTML .= "</td>";
				$ScheduledUpTo = $thisRoomSchedArray[$thisKey]["startTimeUnits"];
				}
			}
	}

function renderComplicatedBlock($roomId) {
	global $thisSlotLength, $thisSlotBeginUnits, $thisSlotEndUnits, $blockHTML;
    $blockHTML = "<td class=\"schedulerGridRoom schedulerGridSlot\" complicatedBlock=\"true\"";
    if ($thisSlotLength > 1)
        $blockHTML .= " rowspan=\"$thisSlotLength\"";
    $blockHTML .= ">";
    $blockHTML .= "<div class=\"scheduleGridComplexDIV\" style=\"height:".($thisSlotLength*20-2)."px;\" roomid=\"$roomId\" ";
    $blockHTML .=      "startTimeUnits=\"$thisSlotBeginUnits\" endTimeUnits=\"$thisSlotEndUnits\">";
    $blockHTML .= "Block too complicated to render</div></td>";
}

function emptySchedBlock($roomId) {
	global $thisSlotLength, $thisSlotBeginUnits, $thisSlotEndUnits;
	$blockHTML = "<td class=\"schedulerGridRoom schedulerGridSlot\"";
	if ($thisSlotLength > 1)
		$blockHTML .= " rowspan=\"$thisSlotLength\"";
	$blockHTML .= ">";
	$blockHTML .= "<div class=\"scheduleGridEmptyDIV\" style = \"height:" . ($thisSlotLength * 19 - 8) . "px\" ";
	$blockHTML .=      "roomid=\"$roomId\" startTimeUnits=\"$thisSlotBeginUnits\" endTimeUnits=\"$thisSlotEndUnits\">";
	$blockHTML .= "&nbsp;</div></td>";
	return $blockHTML;
}

function retrieveSessionInfo() {
	global $link,$message_error;
	$ConStartDatim = CON_START_DATIM;
	$sessionid = isset($_POST["sessionid"]) ? $_POST["sessionid"] : false;
	$query["sessions"] = <<<EOD
SELECT
		S.sessionid, S.title, S.progguiddesc, S.notesforprog, TR.trackname, TY.typename, D.divisionname,
		DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') as starttime,
		DATE_FORMAT(ADDTIME('$ConStartDatim',ADDTIME(SCH.starttime, S.duration)),'%a %l:%i %p') as endtime,
		TIME_FORMAT(S.duration, '%l:%i') AS duration, SCH.roomid, R.roomname
	FROM Sessions S JOIN Tracks TR USING (trackid) JOIN Types TY USING (typeid)
		JOIN Divisions D USING (divisionid) LEFT JOIN Schedule SCH USING (sessionid)
		LEFT JOIN Rooms R USING (roomid)
	WHERE S.sessionid = $sessionid;
EOD;
	$query["participants"] = <<<EOD
SELECT POS.moderator, CD.badgename, P.badgeid, COALESCE(P.pubsname, CONCAT(CD.firstname, ' ', CD.lastname)) AS participantname
	FROM ParticipantOnSession POS JOIN Participants P USING (badgeid) JOIN CongoDump CD USING (badgeid)
	WHERE POS.sessionid = $sessionid;
EOD;
	$resultXML=mysql_query_XML($query);
    if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
        }
	//echo($resultXML->saveXML());
	//exit();
	$xsl = new DomDocument;
	$xsl->load('xsl/schedulerRetrSessInfo.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	if ($html = $xslt->transformToXML($resultXML)) {
			header("Content-Type: text/html"); 
		    echo $html;
			}
		else {
		    trigger_error('XSL transformation failed.', E_USER_ERROR);
			}
	exit();
	
}

function editSchedule() {
	global $link,$message,$message_error;
	usleep(500000);
	$returnTable = isset($_POST["returnTable"]) ? $_POST["returnTable"] : false;
	$editsArray = isset($_POST["editsArray"]) ? $_POST["editsArray"] : false;
	$roomsToDisplayArray = isset($_POST["roomsToDisplayArray"]) ? $_POST["roomsToDisplayArray"] : false;
	if (!$editsArray)
		exit();
	$name = "";
	$email = "";
    get_name_and_email($name, $email); // populates them from session data or db as necessary
    $name = mysql_real_escape_string($name,$link);
    $email = mysql_real_escape_string($email,$link);
    $badgeid = mysql_real_escape_string($_SESSION['badgeid'],$link);
	// this is used for the conflict checker only
	$addToScheduleArray = array();
	// this is used for the conflict checker only
	$deleteScheduleIds = array();
	// these are actually removed from the schedule with a single query -- should include deletes and reschedules
	$deleteScheduleIdList = "";
	// this is for updating the SessionEditHistory table -- should include only actual deletes
	$deleteSessionIdList = "";
	$SchedInsQueryPreamble = "INSERT INTO Schedule (sessionid, roomid, starttime) VALUES ";
	$SchedInsQueryArray = array();
	$SEHInsQu = "INSERT INTO SessionEditHistory (sessionid, badgeid, name, email_address, sessioneditcode, statusid, editdescription) VALUES ";
	$SEHInsQu2 = "";
	//  status 3 is "scheduled"
	$SessStatSchedQu = "UPDATE Sessions SET statusid = 3 WHERE sessionid IN (";
	$newSchedIdArray = array();
	foreach ($editsArray as $i => $thisEdit)
		if ($thisEdit["action"] == "insert") {
				$addToScheduleArray[$thisEdit["sessionid"]] = $thisEdit["starttimeunits"] * 30; // convert to minutes from start of con for conflict checker
				$SchedInsQueryArray[$thisEdit["sessionid"]] = $SchedInsQueryPreamble . "({$thisEdit["sessionid"]}, {$thisEdit["roomid"]},'" . floor($thisEdit["starttimeunits"]/2) . ":" . (($thisEdit["starttimeunits"]%2==1)?"30":"00").":00');";
				// session edit code 4 is "Add to schedule", status 3 is scheduled
				$SEHInsQu2 .= "({$thisEdit["sessionid"]}, \"$badgeid\", \"$name\", \"$email\", 4, 3, \"" . timeDescFromUnits($thisEdit["starttimeunits"]) ." in {$thisEdit["roomid"]}\"),";
				$SessStatSchedQu .= "{$thisEdit["sessionid"]},";
				}
			elseif ($thisEdit["action"] == "delete") {
				$deleteScheduleIds[$thisEdit["scheduleid"]] = 1;
				$deleteScheduleIdList .= $thisEdit["scheduleid"] . ",";
				$deleteSessionIdList .= $thisEdit["sessionid"] . ",";
				// session edit code 5 is "Remove from schedule", status 2 is vetted
				$SEHInsQu2 .= "({$thisEdit["sessionid"]}, \"$badgeid\", \"$name\", \"$email\", 5, 2, null),";
				}
			elseif ($thisEdit["action"] == "reschedule") {
				$addToScheduleArray[$thisEdit["sessionid"]] = $thisEdit["starttimeunits"] * 30; // convert to minutes from start of con for conflict checker
				$SchedInsQueryArray[$thisEdit["sessionid"]] = $SchedInsQueryPreamble . "({$thisEdit["sessionid"]}, {$thisEdit["roomid"]},'" .floor($thisEdit["starttimeunits"]/2).":". (($thisEdit["starttimeunits"]%2==1)?"30":"00").":00');";
				$deleteScheduleIds[$thisEdit["scheduleid"]] = 1;
				$deleteScheduleIdList .= $thisEdit["scheduleid"] . ",";
				// session edit code 7 is "Rescheduled", status 3 is scheduled
				$SEHInsQu2 .= "({$thisEdit["sessionid"]}, \"$badgeid\", \"$name\", \"$email\", 7, 3, \"" . timeDescFromUnits($thisEdit["starttimeunits"]) ." in {$thisEdit["roomid"]}\"),";
				$SessStatSchedQu .= "{$thisEdit["sessionid"]},";
				}
	$deleteScheduleIdList = substr($deleteScheduleIdList,0,-1); //drop extra trailing comma
	$deleteSessionIdList = substr($deleteSessionIdList,0,-1); //drop extra trailing comma
	$SchedInsQuP2 = substr($SchedInsQuP2,0,-1); //drop extra trailing comma
	$SEHInsQu2 = substr($SEHInsQu2,0,-1); //drop extra trailing comma
	$noconflicts = check_room_sched_conflicts($deleteScheduleIds,$addToScheduleArray);
	// details of conflicts stored in $message
	$warnMsg = $message; // save for use later
	
	if (count($SchedInsQueryArray) > 0) {
		foreach ($SchedInsQueryArray as $thisSessionId => $thisQuery) {
			$result = mysql_query_with_error_handling($thisQuery);
			if (!$result) {
			    RenderErrorAjax($message_error);
			    exit();
			    }
			$SchedInsQueryArray[$thisSessionId] = mysql_insert_id($link);
			}
		$SessStatSchedQu = substr($SessStatSchedQu,0,-1) . ");"; //drop extra trailing comma and close quert
		$result = mysql_query_with_error_handling($SessStatSchedQu);
		if (!$result) {
		    RenderErrorAjax($message_error);
		    exit();
		    }
		}
	if ($SEHInsQu2) {
		$SEHInsQu = $SEHInsQu . $SEHInsQu2 . ";";
		$result = mysql_query_with_error_handling($SEHInsQu);
		if (!$result) {
		    RenderErrorAjax($message_error);
		    exit();
		    }
		}
	if ($deleteScheduleIdList) {
		$deleteQuery = "DELETE FROM Schedule WHERE scheduleid in ($deleteScheduleIdList);";
		$result = mysql_query_with_error_handling($deleteQuery);
		if (!$result) {
		    RenderErrorAjax($message_error);
		    exit();
		    }
		}
	if ($deleteSessionIdList) {
		//  status 2 is "vetted"
		$SessStatVettedQu = "UPDATE Sessions SET statusid = 2 WHERE sessionid IN ($deleteSessionIdList);";
		$result = mysql_query_with_error_handling($SessStatVettedQu);
		if (!$result) {
		    RenderErrorAjax($message_error);
		    exit();
		    }
		}
	if ($returnTable == "true") {
			retrieveRoomsTable();
			echo "<div id=\"warningsDivContent\">$warnMsg</div>";
			}
		else {
			echo($warnMsg);
			foreach ($SchedInsQueryArray as $thisSessionId => $thisScheduleId) {
				echo "<div class=\"insertedScheduleId\" sessionId=\"$thisSessionId\" scheduleId=\"$thisScheduleId\"></div>";
				}
			
			}
}

function retrieveSessions() {
    global $link,$message_error;
    $currSessionIdArray = isset($_POST["currSessionIdArray"]) ? $_POST["currSessionIdArray"] : false;
	$trackId = intval($_POST["trackId"]);
    $typeId = intval($_POST["typeId"]);
    $divisionId = intval($_POST["divisionId"]);
    $sessionId = intval($_POST["sessionId"]);
    $title = mysql_real_escape_string(stripslashes($_POST["title"]));
	$query["sessions"] = <<<EOD
SELECT S.sessionid, S.title, S.progguiddesc, TR.trackname, TY.typename, D.divisionname,
	FLOOR((HOUR(S.duration) * 60 + MINUTE(S.duration) + 29) / 30) AS durationUnits,
	S.duration
	FROM Sessions S JOIN Tracks TR USING (trackid) JOIN Types TY USING (typeid)
		JOIN Divisions D USING (divisionid)
	WHERE S.statusid IN (2,3,7) AND S.sessionid NOT IN (SELECT sessionid FROM Schedule)
EOD;
	if ($trackId != 0) {
		$query["sessions"] .= " AND S.trackid = $trackId";
		}
	if ($typeId != 0) {
		$query["sessions"] .= " AND S.typeid = $typeId";
		}
	if ($divisionId != 0) {
		$query["sessions"] .= " AND S.divisionid = $divisionId";
		}
	if ($sessionId != 0) {
		$query["sessions"] .= " AND S.sessionid = $sessionId";
		}
	if ($title != "") {
		$query["sessions"] .= " AND S.title LIKE '%$title%'";
		}
	$currSessionIdList = "";
	if ($currSessionIdArray)
		foreach ($currSessionIdArray as $id)
			$currSessionIdList .= intval($id).",";
	if ($currSessionIdList) {
		$currSessionIdList = substr($currSessionIdList,0,-1); //drop extra trailing comma
		$query["sessions"] .= " AND S.sessionid NOT IN ($currSessionIdList)";
		}
	$resultXML=mysql_query_XML($query);
    if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
        }
	//echo($resultXML->saveXML());
	//exit();
	$xpath = new DOMXpath($resultXML);
	$numRows = $xpath->evaluate("count(/doc/query/row)");
	// signal found no new sessions
	if ($numRows == 0) {
		header("Content-Type: text");
		echo "noNewSessionsFound";
		exit();
		}
	$xsl = new DomDocument;
	$xsl->load('xsl/schedulerRetrSess.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	if ($html = $xslt->transformToXML($resultXML)) {
			header("Content-Type: text/html"); 
		    echo $html;
			}
		else {
		    trigger_error('XSL transformation failed.', E_USER_ERROR);
			}
	exit();
    }

function update_participant() {
	$searchString = mysql_real_escape_string(stripslashes($_POST["searchString"]));
	if ($searchString=="")
		exit();
	if (is_numeric($searchString)) {
			$query["searchParticipants"] = <<<EOD
			SELECT
			        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename
			    FROM
						 Participants P
					JOIN CongoDump CD ON P.badgeid = CD.badgeid
			    WHERE
			        P.badgeid = "$searchString"
			    ORDER BY
			        CD.lastname, CD.firstname
EOD;
			}
		else {
			$searchString='%'.$searchString.'%';
			$query["searchParticipants"] = <<<EOD
			SELECT
			        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename
			    FROM
						 Participants P
					JOIN CongoDump CD ON P.badgeid = CD.badgeid
			    WHERE
			           P.pubsname LIKE "$searchString"
					OR CD.lastname LIKE "$searchString"
					OR CD.firstname LIKE "$searchString"
					OR CD.badgename LIKE "$searchString"
			    ORDER BY
			        CD.lastname, CD.firstname
EOD;
			}
	$xml=mysql_query_XML($query);
    if (!$xml) {
        echo $message_error;
        exit();
        }
	$xsl = new DomDocument;
	$xsl->load('xsl/AdminParticipants.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	if ($html = $xslt->transformToXML($xml)) {
			header("Content-Type: text/html"); 
		    echo $html;
			}
		else {
		    trigger_error('XSL transformation failed.', E_USER_ERROR);
			}
	exit();
}
// Start here.  Should be AJAX requests only
if (!$ajax_request_action=$_POST["ajax_request_action"])
	exit();
switch ($ajax_request_action) {
	case "editSchedule":
		editSchedule();
		break;
	case "retrieveRoomsTable":
		retrieveRoomsTable();
		break;
	case "retrieveSessionInfo":
		retrieveSessionInfo();
		break;
	case "retrieveSessions":
		retrieveSessions();
		break;
	case "update_participant":
		update_participant();
		break;
	default:
		exit();
	}
?>
