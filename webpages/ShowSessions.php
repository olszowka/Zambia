<?php
//  Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once ('StaffCommonCode.php');
require_once ('RenderSearchSessionResults.php');
$trackid = getInt("track", "");
$typeid = getInt("type", "");
$statusid = getInt("status", "");
$sessionid = getInt("sessionid", "");
$divisionid = getInt("divisionid", "");
$searchTitle = getString("searchtitle");
if (is_null($searchTitle)) {
    $searchTitle = "";
}
$encTitle = urlencode($searchTitle);
$tags = getArrayOfInts("tags", array());
$_SESSION['return_to_page'] = "ShowSessions.php?status=$statusid&track=$trackid&type=$typeid&sessionid=$sessionid";
$_SESSION['return_to_page'] .= "&divisionid=$divisionid&searchtitle=$encTitle";
$tagmatch = getString("tagmatch");
foreach ($tags as $tag) {
    $_SESSION['return_to_page'] .= "&tags[]=$tag";
}
if (!empty($tagmatch)) {
    $_SESSION['return_to_page'] .= "&tagmatch=" . urlencode($tagmatch);
}
$sessionSearchArray = array();
$sessionSearchArray['trackidList'] = strval($trackid);
$sessionSearchArray['typeidList'] = strval($typeid);
$sessionSearchArray['statusidList'] = strval($statusid);
$sessionSearchArray['sessionid'] = strval($sessionid);
$sessionSearchArray['divisionid'] = strval($divisionid);
$sessionSearchArray['searchTitle'] = $searchTitle;
$sessionSearchArray['tagmatch'] = $tagmatch;
$sessionSearchArray['tagidArray'] = $tags;
RenderSearchSessionResults($sessionSearchArray);
?>
