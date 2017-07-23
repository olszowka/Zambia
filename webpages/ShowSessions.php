<?php
//	Copyright (c) 2011-2017 The Zambia Group. All rights reserved. See copyright document for more details.
    require_once ('StaffCommonCode.php');
    require_once ('RenderSearchSessionResults.php');
    if (!empty($_POST["track"]) or !empty($_POST["status"]) or !empty($_POST["type"]) or !empty($_POST["sessionid"])
        or !empty($_POST["divisionid"]) or !empty($_POST["searchtitle"])) {
            $status = isset($_POST["status"]) ? $_POST["status"] : "";
            $track = isset($_POST["track"]) ? $_POST["track"] : "";
            $type = isset($_POST["type"]) ? $_POST["type"] : "";
            $sessionid = isset($_POST["sessionid"]) ? $_POST["sessionid"] : "";
            $divisionid = isset($_POST["divisionid"]) ? $_POST["divisionid"] : "";
            $searchtitle = isset($_POST["searchtitle"]) ? $_POST["searchtitle"] : "";
            }
        else {
            $status = isset($_GET["status"])? $_GET["status"] : "";
            $track = isset($_GET["track"]) ? $_GET["track"] : "";
            $type = isset($_GET["type"]) ? $_GET["type"] : "";
            $sessionid = isset($_GET["sessionid"]) ? $_GET["sessionid"] : "";
            $divisionid = isset($_GET["divisionid"]) ? $_GET["divisionid"] : "";
            $searchtitle = isset($_GET["searchtitle"]) ? $_GET["searchtitle"] : "";
            }
    //echo ("ShowSessions -- Divisionid: $divisionid:<BR>\n");
    //echo ("ShowSessions -- Track: $track:<BR>\n");
    if (!is_numeric($sessionid)) {
        $sessionid="";
        }
    $foo="ShowSessions.php?status=$status&track=$track&type=$type&sessionid=$sessionid";
    $_SESSION['return_to_page']=$foo."&divisionid=$divisionid&searchtitle=$searchtitle";
    $trackidlist=$track;
    $statusidlist=$status;
    $typeidlist=$type;
    RenderSearchSessionResults($trackidlist,$statusidlist,$typeidlist,$sessionid,$divisionid,$searchtitle);
?>
