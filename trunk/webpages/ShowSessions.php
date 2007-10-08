<?php
  require_once ('db_functions.php');
  require_once ('data_functions.php');
  require_once ('RenderSearchSessionResults.php');
  session_start();
  if ($_POST["track"] or $_POST["status"] or $_POST["type"]) {
    $status=$_POST["status"]; 
    $track=$_POST["track"];
    $type=$_POST["type"]; 
    }
  else {
    $status=$_GET["status"]; 
    $track=$_GET["track"]; 
    $type=$_GET["type"];
    }
  $_SESSION['return_to_page']="ShowSessions.php?status=$status&track=$track&type=$type";
  RenderSearchSessionResults($track,$status,$type);
?>
