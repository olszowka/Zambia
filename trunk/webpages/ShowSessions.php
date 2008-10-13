<?php
    require_once ('db_functions.php');
    require_once ('data_functions.php');
    require_once ('RenderSearchSessionResults.php');
    session_start();
    if ($_POST["track"] or $_POST["status"] or $_POST["type"] or $_POST["sessionid"]) {
            $status=$_POST["status"]; 
            $track=$_POST["track"];
            $type=$_POST["type"];
            $sessionid=$_POST["sessionid"]; 
            }
        else {
            $status=$_GET["status"]; 
            $track=$_GET["track"]; 
            $type=$_GET["type"];
            $sessionid=$_GET["sessionid"];
            }
    if (!is_numeric($sessionid)) {
        $sessionid="";
        }
    $_SESSION['return_to_page']="ShowSessions.php?status=$status&track=$track&type=$type&sessionid=$sessionid";
    $trackidlist=$track;
    $statusidlist=$status;
    $typeidlist=$type;
    RenderSearchSessionResults($trackidlist,$statusidlist,$typeidlist,$sessionid);
?>
