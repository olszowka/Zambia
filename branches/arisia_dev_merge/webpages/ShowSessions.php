<?php
    require_once ('StaffCommonCode.php');
    require_once ('RenderSearchSessionResults.php');
    if ($_POST["track"] or $_POST["status"] or $_POST["type"] or $_POST["sessionid"]
        or $_POST["divivionid"] or $_POST["searchtitle"]) {
            $status=$_POST["status"]; 
            $track=$_POST["track"];
            $type=$_POST["type"];
            $sessionid=$_POST["sessionid"];
            $divisionid=$_POST["divisionid"];
            $searchtitle=$_POST["searchtitle"];
            }
        else {
            $status=$_GET["status"]; 
            $track=$_GET["track"]; 
            $type=$_GET["type"];
            $sessionid=$_GET["sessionid"];
            $divisionid=$_GET["divisionid"];
            $searchtitle=$_GET["searchtitle"];
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
