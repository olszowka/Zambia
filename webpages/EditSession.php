<?php
    require_once('StaffCommonCode.php');
    require ('RenderEditCreateSession.php');
    global $message2;
    $error=false;
    $message_error="";
    $id=$_GET["id"];
    if ($id=="") {
	$message_error.="The id parameter is required.";
        $error=true;
        }
    $id=intval($id);
    if ($id<1) {
        $message_error.="The id parameter must be a valid row index.";
        $error=true;
        }
    prepare_db();
    $status=retrieve_session_from_db($id);
    if ($status==-3) {
        $message_error.="Error retrieving record from database. ".$message2;
        $error=true;
        }
    if ($status==-2) {
        $message_error.="Session record with id=".$id." not found (or error with Session primary key).";
        $error=true;
        }
    $message_warn="";
    $action="edit";
    RenderEditCreateSession($action,$session,$message_warn,$message_error);
    exit();
?>
