<?php
//	Copyright (c) 2011-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once ('StaffCommonCode.php');
require_once ('RenderSearchSessionResults.php');
$trackid = getInt("track", "");
$typeid = getInt("type", "");
$statusid = getInt("status", "");
$sessionid = getInt("sessionid", "");
$divisionid = getInt("divisionid", "");
$searchTitle = getString("searchtitle");
$encTitle = urlencode($searchTitle);
$tags = getArrayOfInts("tags", array());
$_SESSION['return_to_page'] = "ShowSessions.php?status=$statusid&track=$trackid&type=$typeid&sessionid=$sessionid";
$_SESSION['return_to_page'] .= "&divisionid=$divisionid&searchtitle=$encTitle";
$sessionSearchArray = array();
$sessionSearchArray['trackidList'] = strval($trackid);
$sessionSearchArray['typeidList'] = strval($typeid);
$sessionSearchArray['statusidList'] = strval($statusid);
$sessionSearchArray['sessionid'] = strval($sessionid);
$sessionSearchArray['divisionid'] = strval($divisionid);
$sessionSearchArray['searchtitle'] = $searchTitle;
$sessionSearchArray['tagmatch'] = getString("tagmatch");
$sessionSearchArray['tagidArray'] = $tags;
RenderSearchSessionResults($sessionSearchArray);
?>
