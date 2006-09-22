<?php
  require_once ('db_functions.php');
  require_once ('data_functions.php');
  require_once ('RenderSearchSessionResults.php');
  session_start();
  if ($_POST["track"]or $_POST["status"]) {
    $status=$_POST["status"]; 
    $track=$_POST["track"]; 
    }
  else {
    $status=$_GET["status"]; 
    $track=$_GET["track"]; 
    }
  $_SESSION['return_to_page']="ShowSessions.php?status=$status&track=$track";
  RenderSearchSessionResults($track,$status);
?>
